<?php

namespace App\Http\Livewire\Usage;

use App\Models\Gallery;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

/**
 * Composant Livewire pour la gestion de la galerie photos d'un article.
 * Gère l'upload multiple, la suppression, le réordonnancement et le point focal.
 */
class PostGallery extends Component
{
    use WithFileUploads;

    public $postId;
    public $newImages  = [];
    public $tempImages = [];
    public $sessionToken;

    /** Point focal de la première image (0–100). */
    public float $focalX = 50.0;
    public float $focalY = 50.0;

    /** Gestion de l'ajout de vidéo */
    public bool $showVideoForm = false;
    public string $videoUrl = '';
    public $newVideo = null;
    public ?string $videoThumbnailData = null;
    public ?string $videoError = null;

    protected $listeners = [
        'gallerySaved'      => '$refresh',
        'focalPointUpdated' => 'applyFocalPoint',
        'autoAddImage'      => 'autoAddImage',
        'autoAddVideo'      => 'autoAddVideo',
    ];

    protected function rules()
    {
        return [
            'newImages.*' => 'image|max:10240',
            'newVideo'    => 'nullable|mimes:mp4,webm,mov|max:102400',
        ];
    }

    protected $messages = [
        'newImages.*.image' => 'Chaque fichier doit être une image (jpg, png, gif, webp).',
        'newImages.*.max'   => 'Chaque image ne doit pas dépasser 10 Mo.',
        'newVideo.mimes'    => 'La vidéo doit être au format MP4, WebM ou MOV.',
        'newVideo.max'      => 'La vidéo ne doit pas dépasser 100 Mo.',
    ];

    public function mount($postId = null)
    {
        $this->postId = $postId;

        $this->sessionToken = (string) \Illuminate\Support\Str::uuid();
        session(['post_gallery_token' => $this->sessionToken]);

        if ($this->postId) {
            $gallery = Gallery::where('post_id', $this->postId)->first();
            if ($gallery) {
                $this->tempImages = collect($gallery->images)
                    ->filter(fn($v) => is_array($v) && isset($v['path'])) // exclure focal_x/focal_y
                    ->sortBy('order')
                    ->values()
                    ->toArray();

                $fp = $gallery->getFocalPoint();
                $this->focalX = $fp['x'];
                $this->focalY = $fp['y'];
            }
        }

        $this->syncMemory();
    }

    /**
     * Sauvegarde l'état (images + focal point) dans le cache serveur.
     */
    private function syncMemory(): void
    {
        \Illuminate\Support\Facades\Cache::put(
            "gallery_temp_{$this->sessionToken}",
            [
                'images'  => $this->tempImages,
                'focal_x' => $this->focalX,
                'focal_y' => $this->focalY,
            ],
            3600
        );
    }

    /**
     * Récepteur de l'événement émis par la modale FocalPointPicker.
     *
     * @param float $x
     * @param float $y
     */
    public function applyFocalPoint(float $x, float $y): void
    {
        $this->focalX = round(max(0, min(100, $x)), 2);
        $this->focalY = round(max(0, min(100, $y)), 2);
        $this->syncMemory();
    }

    /**
     * Ajoute automatiquement une image insérée dans l'éditeur TinyMCE à la galerie temporaire.
     * L'enregistrement définitif en BDD se fera lors de la sauvegarde de l'article.
     *
     * @param array|string $data Données de l'image insérée (path, filename).
     */
    public function autoAddImage($data): void
    {
        $path = is_array($data) ? ($data['path'] ?? null) : $data;
        $filename = is_array($data) ? ($data['filename'] ?? basename($path)) : basename($path);
        if (!$path) return;

        $cleanPath = parse_url($path, PHP_URL_PATH) ?: $path;

        // Vérification anti-doublon
        $alreadyExists = collect($this->tempImages)->contains(function ($item) use ($cleanPath, $filename) {
            return ($item['path'] ?? '') === $cleanPath || ($item['filename'] ?? '') === $filename;
        });

        if ($alreadyExists) return;

        $disk = Storage::disk('public');
        $relativeSrc = ltrim(str_replace('/storage/', '', $cleanPath), '/');

        if ($disk->exists($relativeSrc)) {
            $folderName = "temp_{$this->sessionToken}";
            $targetDir  = "galleries/{$folderName}";
            if (!$disk->exists($targetDir)) {
                $disk->makeDirectory($targetDir);
            }

            $safeBasename = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($filename));
            $newFilename = time() . '_' . $safeBasename;
            $newRelativePath = "{$targetDir}/{$newFilename}";

            // On copie le fichier afin que la galerie ait sa copie dédiée
            $disk->copy($relativeSrc, $newRelativePath);

            // Optimisation / redimensionnement
            $fullPath = storage_path('app/public/' . $newRelativePath);
            if (file_exists($fullPath)) {
                try {
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($fullPath);
                    $img->scaleDown(1024, 1024);
                    $img->save($fullPath, quality: 80);
                } catch (\Throwable $e) {
                    // Conserve le fichier copié sans redimensionnement
                }
            }

            $this->tempImages[] = [
                'type'     => 'image',
                'path'     => '/storage/' . $newRelativePath,
                'filename' => $filename,
                'order'    => count($this->tempImages) + 1,
            ];

            $this->syncMemory();
        }
    }

    public function updatedNewImages()
    {
        $this->validate(['newImages.*' => 'image|max:10240']);
        $this->saveNewImages();
    }

    private function saveNewImages(): void
    {
        if (empty($this->newImages)) return;

        $manager    = new ImageManager(new Driver());
        $folderName = "temp_{$this->sessionToken}";

        foreach ($this->newImages as $image) {
            $filename = time() . '_' . $image->getClientOriginalName();
            $path     = $image->storeAs("galleries/{$folderName}", $filename, 'public');
            $fullPath = storage_path('app/public/' . $path);

            $img = $manager->read($fullPath);
            $img->scaleDown(1024, 1024);
            $img->save($fullPath, quality: 80);

            $this->tempImages[] = [
                'type'     => 'image',
                'path'     => '/storage/' . $path,
                'filename' => $image->getClientOriginalName(),
                'order'    => count($this->tempImages) + 1,
            ];
        }

        $this->syncMemory();
        $this->newImages = [];
    }

    /**
     * Ouvre ou ferme le panneau d'ajout de vidéo.
     */
    public function toggleVideoForm(): void
    {
        $this->showVideoForm = !$this->showVideoForm;
        $this->videoError = null;
    }

    /**
     * Réinitialise les champs du formulaire d'ajout de vidéo.
     */
    public function resetVideoForm(): void
    {
        $this->videoUrl = '';
        $this->newVideo = null;
        $this->videoThumbnailData = null;
        $this->videoError = null;
        $this->showVideoForm = false;
    }

    /**
     * Ajoute automatiquement une vidéo insérée dans l'éditeur (YouTube, Google Drive, ou fichier vidéo local)
     * à la galerie temporaire.
     *
     * @param string|array $data URL ou informations de la vidéo.
     * @return bool True si la vidéo a été ajoutée ou existe déjà, false sinon.
     */
    public function autoAddVideo($data): bool
    {
        $url = is_array($data) ? ($data['url'] ?? $data['path'] ?? '') : (string) $data;
        $url = trim($url);
        if (empty($url)) return false;

        $folderName = "temp_{$this->sessionToken}";
        $targetDir  = "galleries/{$folderName}";
        $disk       = Storage::disk('public');
        if (!$disk->exists($targetDir)) {
            $disk->makeDirectory($targetDir);
        }

        // 1. YouTube
        if ($ytId = self::extractYoutubeId($url)) {
            $cleanUrl = "https://www.youtube.com/watch?v={$ytId}";

            // Vérification anti-doublon
            $alreadyExists = collect($this->tempImages)->contains(function ($item) use ($ytId, $cleanUrl) {
                if (($item['type'] ?? '') !== 'video') return false;
                $p = $item['path'] ?? '';
                return str_contains($p, $ytId) || $p === $cleanUrl;
            });

            if ($alreadyExists) return true;

            $thumbFilename = "yt_{$ytId}_" . time() . ".jpg";
            $thumbPath = "{$targetDir}/{$thumbFilename}";
            $thumbUrl = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
            $finalThumb = $thumbUrl;

            try {
                $context = stream_context_create(['http' => ['timeout' => 3, 'user_agent' => 'Mozilla/5.0']]);
                $contents = @file_get_contents($thumbUrl, false, $context);
                if ($contents && strlen($contents) > 500) {
                    $disk->put($thumbPath, $contents);
                    $finalThumb = '/storage/' . $thumbPath;
                }
            } catch (\Throwable $e) {
                $finalThumb = $thumbUrl;
            }

            $this->tempImages[] = [
                'type'      => 'video',
                'provider'  => 'youtube',
                'path'      => $cleanUrl,
                'thumbnail' => $finalThumb,
                'filename'  => "Vidéo YouTube ({$ytId})",
                'order'     => count($this->tempImages) + 1,
            ];

            $this->syncMemory();
            return true;
        }

        // 2. Google Drive
        if ($driveId = self::extractDriveId($url)) {
            $cleanUrl = "https://drive.google.com/file/d/{$driveId}/preview";

            // Vérification anti-doublon
            $alreadyExists = collect($this->tempImages)->contains(function ($item) use ($driveId, $cleanUrl) {
                if (($item['type'] ?? '') !== 'video') return false;
                $p = $item['path'] ?? '';
                return str_contains($p, $driveId) || $p === $cleanUrl;
            });

            if ($alreadyExists) return true;

            $thumbFilename = "drive_{$driveId}_" . time() . ".jpg";
            $thumbPath = "{$targetDir}/{$thumbFilename}";
            $driveThumbUrl = "https://drive.google.com/thumbnail?id={$driveId}&sz=w1280";
            $finalThumb = null;

            try {
                $context = stream_context_create(['http' => ['timeout' => 3, 'user_agent' => 'Mozilla/5.0']]);
                $contents = @file_get_contents($driveThumbUrl, false, $context);
                if ($contents && strlen($contents) > 500) {
                    $disk->put($thumbPath, $contents);
                    $finalThumb = '/storage/' . $thumbPath;
                }
            } catch (\Throwable $e) {
                // Ignore
            }

            if (!$finalThumb) {
                $finalThumb = self::defaultVideoPoster();
            }

            $this->tempImages[] = [
                'type'      => 'video',
                'provider'  => 'drive',
                'path'      => $cleanUrl,
                'thumbnail' => $finalThumb,
                'filename'  => "Vidéo Google Drive",
                'order'     => count($this->tempImages) + 1,
            ];

            $this->syncMemory();
            return true;
        }

        // 3. Fichier vidéo local (ex: /storage/uploads/xxx.mp4)
        $cleanPath = parse_url($url, PHP_URL_PATH) ?: $url;
        $relativeSrc = ltrim(str_replace('/storage/', '', $cleanPath), '/');

        if ($disk->exists($relativeSrc)) {
            $filename = basename($cleanPath);
            $alreadyExists = collect($this->tempImages)->contains(function ($item) use ($cleanPath, $filename) {
                return ($item['path'] ?? '') === $cleanPath || ($item['filename'] ?? '') === $filename;
            });

            if ($alreadyExists) return true;

            $ext = pathinfo($filename, PATHINFO_EXTENSION) ?: 'mp4';
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '', pathinfo($filename, PATHINFO_FILENAME));
            $newFilename = 'vid_' . time() . '_' . $safeName . '.' . $ext;
            $newRelativePath = "{$targetDir}/{$newFilename}";

            $disk->copy($relativeSrc, $newRelativePath);

            $this->tempImages[] = [
                'type'      => 'video',
                'provider'  => 'local',
                'path'      => '/storage/' . $newRelativePath,
                'thumbnail' => self::defaultVideoPoster(),
                'filename'  => $filename,
                'order'     => count($this->tempImages) + 1,
            ];

            $this->syncMemory();
            return true;
        }

        return false;
    }

    /**
     * Ajoute une vidéo externe (YouTube ou Google Drive) à la galerie.
     */
    public function addExternalVideo(): void
    {
        $this->videoError = null;
        $url = trim($this->videoUrl);

        if (empty($url)) {
            $this->videoError = 'Veuillez saisir une URL de vidéo.';
            return;
        }

        $added = $this->autoAddVideo($url);
        if ($added) {
            $this->resetVideoForm();
        } else {
            $this->videoError = "L'URL renseignée n'est pas reconnue comme un lien YouTube ou Google Drive valide.";
        }
    }

    /**
     * Traitement de l'envoi d'un fichier vidéo local (MP4 / WebM).
     */
    public function updatedNewVideo()
    {
        $this->validate(['newVideo' => 'mimes:mp4,webm,mov|max:102400']);
        $this->saveNewVideo();
    }

    private function saveNewVideo(): void
    {
        if (!$this->newVideo) return;

        $folderName = "temp_{$this->sessionToken}";
        $targetDir  = "galleries/{$folderName}";
        $disk       = Storage::disk('public');
        if (!$disk->exists($targetDir)) {
            $disk->makeDirectory($targetDir);
        }

        $extension = $this->newVideo->getClientOriginalExtension() ?: 'mp4';
        $videoFilename = 'vid_' . time() . '.' . $extension;
        $videoPath = $this->newVideo->storeAs($targetDir, $videoFilename, 'public');

        // Génération ou récupération de la vignette
        $thumbPath = self::defaultVideoPoster();
        if ($this->videoThumbnailData && str_contains($this->videoThumbnailData, 'base64,')) {
            try {
                $base64 = explode('base64,', $this->videoThumbnailData)[1];
                $decoded = base64_decode($base64);
                if ($decoded) {
                    $thumbFilename = 'thumb_' . time() . '.jpg';
                    $disk->put("{$targetDir}/{$thumbFilename}", $decoded);
                    $thumbPath = '/storage/' . "{$targetDir}/{$thumbFilename}";
                }
            } catch (\Throwable $e) {
                // Utilise le poster par défaut en cas d'échec
            }
        }

        $this->tempImages[] = [
            'type'      => 'video',
            'provider'  => 'local',
            'path'      => '/storage/' . $videoPath,
            'thumbnail' => $thumbPath,
            'filename'  => $this->newVideo->getClientOriginalName(),
            'order'     => count($this->tempImages) + 1,
        ];

        $this->syncMemory();
        $this->resetVideoForm();
    }

    /**
     * Extrait l'identifiant YouTube d'une URL.
     */
    public static function extractYoutubeId(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extrait l'identifiant Google Drive d'une URL.
     */
    public static function extractDriveId(string $url): ?string
    {
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Poster par défaut pour les vidéos sans vignette extraite.
     */
    public static function defaultVideoPoster(): string
    {
        return '/img/default-post-bg.png';
    }

    public function removeImage(string $path): void
    {
        $item = collect($this->tempImages)->firstWhere('path', $path);

        if (str_contains($path, "temp_{$this->sessionToken}")) {
            $storagePath = str_replace('/storage/', '', $path);
            Storage::disk('public')->delete($storagePath);
        }

        if ($item && !empty($item['thumbnail']) && str_contains($item['thumbnail'], "temp_{$this->sessionToken}")) {
            $storageThumb = str_replace('/storage/', '', $item['thumbnail']);
            Storage::disk('public')->delete($storageThumb);
        }

        $this->tempImages = collect($this->tempImages)
            ->reject(fn ($img) => $img['path'] === $path)
            ->values()
            ->map(fn ($img, $index) => array_merge($img, ['order' => $index + 1]))
            ->toArray();

        $this->syncMemory();
    }

    public function moveUp(int $currentOrder): void
    {
        $this->reorder($currentOrder, $currentOrder - 1);
    }

    public function moveDown(int $currentOrder): void
    {
        $this->reorder($currentOrder, $currentOrder + 1);
    }

    private function reorder(int $from, int $to): void
    {
        $images = collect($this->tempImages)->sortBy('order')->values();

        $fromIndex = $from - 1;
        $toIndex   = $to - 1;

        if ($toIndex < 0 || $toIndex >= $images->count()) return;

        $temp              = $images[$fromIndex];
        $images[$fromIndex] = $images[$toIndex];
        $images[$toIndex]  = $temp;

        $this->tempImages = $images
            ->map(fn ($img, $i) => array_merge($img, ['order' => $i + 1]))
            ->toArray();

        $this->syncMemory();
    }

    /**
     * PROCESSUS DE SYNCHRONISATION GLOBAL
     * Réconcilie le cache (images + focal point) avec le disque et la BDD.
     */
    public static function processTempGallery($postId, $sessionToken): void
    {
        if (!$sessionToken) return;

        $cached = \Illuminate\Support\Facades\Cache::get("gallery_temp_{$sessionToken}");
        if ($cached === null) return;

        // Rétrocompatibilité : ancien cache = tableau plat d'images
        if (isset($cached[0]) || (is_array($cached) && !isset($cached['images']))) {
            $tempImages = $cached;
            $focalX     = 50.0;
            $focalY     = 50.0;
        } else {
            $tempImages = $cached['images']  ?? [];
            $focalX     = (float) ($cached['focal_x'] ?? 50.0);
            $focalY     = (float) ($cached['focal_y'] ?? 50.0);
        }

        $gallery      = Gallery::firstOrCreate(['post_id' => $postId], ['images' => []]);
        $existingPaths = collect($gallery->images ?? [])
            ->pluck('path')
            ->toArray();
        $newPaths = collect($tempImages)->pluck('path')->toArray();

        // 1. Suppression physique des images/médias retirés
        $deletedPaths = array_diff($existingPaths, $newPaths);
        foreach ($deletedPaths as $dPath) {
            if (!str_starts_with($dPath, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $dPath));
            }
        }

        // 2. Déplacement physique des médias brouillon vers le dossier définitif
        $finalImages = array_map(function ($img) use ($postId, $sessionToken) {
            $updated = $img;

            // Déplacement du fichier principal si stocké en local
            if (isset($updated['path']) && str_contains($updated['path'], "galleries/temp_{$sessionToken}")) {
                $oldStorage = str_replace('/storage/', '', $updated['path']);
                $newStorage = str_replace(
                    "galleries/temp_{$sessionToken}",
                    "galleries/{$postId}",
                    $oldStorage
                );

                if (!Storage::disk('public')->exists("galleries/{$postId}")) {
                    Storage::disk('public')->makeDirectory("galleries/{$postId}");
                }
                if (Storage::disk('public')->exists($oldStorage)) {
                    Storage::disk('public')->move($oldStorage, $newStorage);
                }

                $updated['path'] = '/storage/' . $newStorage;
            }

            // Déplacement de la vignette si stockée en local
            if (isset($updated['thumbnail']) && str_contains($updated['thumbnail'], "galleries/temp_{$sessionToken}")) {
                $oldThumbStorage = str_replace('/storage/', '', $updated['thumbnail']);
                $newThumbStorage = str_replace(
                    "galleries/temp_{$sessionToken}",
                    "galleries/{$postId}",
                    $oldThumbStorage
                );

                if (!Storage::disk('public')->exists("galleries/{$postId}")) {
                    Storage::disk('public')->makeDirectory("galleries/{$postId}");
                }
                if (Storage::disk('public')->exists($oldThumbStorage)) {
                    Storage::disk('public')->move($oldThumbStorage, $newThumbStorage);
                }

                $updated['thumbnail'] = '/storage/' . $newThumbStorage;
            }

            return $updated;
        }, $tempImages);

        // 3. Persistance — images et focal point dans deux colonnes séparées
        $gallery->update([
            'images'      => array_values($finalImages),
            'focal_point' => ['x' => round($focalX, 2), 'y' => round($focalY, 2)],
        ]);

        // 4. Nettoyage du dossier temp
        if (Storage::disk('public')->exists("galleries/temp_{$sessionToken}")) {
            Storage::disk('public')->deleteDirectory("galleries/temp_{$sessionToken}");
        }

        // 5. Suppression de la galerie si vide
        if (count($finalImages) === 0) {
            $gallery->delete();
        }

        \Illuminate\Support\Facades\Cache::forget("gallery_temp_{$sessionToken}");
        session()->forget('post_gallery_token');
    }

    public function render()
    {
        $images = collect($this->tempImages)->sortBy('order')->values()->all();

        return view('livewire.usage.post-gallery', [
            'hasImages'  => count($images) > 0,
            'imagesList' => $images,
        ]);
    }
}
