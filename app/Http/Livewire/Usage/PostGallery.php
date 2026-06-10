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

    protected $listeners = [
        'gallerySaved'      => '$refresh',
        'focalPointUpdated' => 'applyFocalPoint',
    ];

    protected function rules()
    {
        return [
            'newImages.*' => 'image|max:10240',
        ];
    }

    protected $messages = [
        'newImages.*.image' => 'Chaque fichier doit être une image (jpg, png, gif, webp).',
        'newImages.*.max'   => 'Chaque image ne doit pas dépasser 10 Mo.',
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

    public function updatedNewImages()
    {
        $this->validate();
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
                'path'     => '/storage/' . $path,
                'filename' => $image->getClientOriginalName(),
                'order'    => count($this->tempImages) + 1,
            ];
        }

        $this->syncMemory();
        $this->newImages = [];
    }

    public function removeImage(string $path): void
    {
        if (str_contains($path, "temp_{$this->sessionToken}")) {
            $storagePath = str_replace('/storage/', '', $path);
            Storage::disk('public')->delete($storagePath);
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

        // 1. Suppression physique des images retirées
        $deletedPaths = array_diff($existingPaths, $newPaths);
        foreach ($deletedPaths as $dPath) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $dPath));
        }

        // 2. Déplacement physique des images brouillon vers le dossier définitif
        $finalImages = array_map(function ($img) use ($postId, $sessionToken) {
            if (str_contains($img['path'], "galleries/temp_{$sessionToken}")) {
                $oldStorage = str_replace('/storage/', '', $img['path']);
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

                return [
                    'path'     => '/storage/' . $newStorage,
                    'filename' => $img['filename'],
                    'order'    => $img['order'],
                ];
            }
            return $img;
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
