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
 * Gère l'upload multiple, la suppression et le réordonnancement des images.
 */
class PostGallery extends Component
{
    use WithFileUploads;

    public $postId;
    public $newImages = [];
    public $tempImages = [];
    public $sessionToken;

    protected $listeners = ['gallerySaved' => '$refresh'];

    protected function rules()
    {
        return [
            'newImages.*' => 'image|max:10240', // 10 Mo max par image
        ];
    }

    protected $messages = [
        'newImages.*.image' => 'Chaque fichier doit être une image (jpg, png, gif, webp).',
        'newImages.*.max'   => 'Chaque image ne doit pas dépasser 10 Mo.',
    ];

    public function mount($postId = null)
    {
        $this->postId = $postId;
        
        // On génère un jeton unique par démarrage du composant et on le pousse en Session
        // pour que EditPostManager puisse le lire lors du Save global.
        $this->sessionToken = (string) \Illuminate\Support\Str::uuid();
        session(['post_gallery_token' => $this->sessionToken]);

        if ($this->postId) {
            $gallery = Gallery::where('post_id', $this->postId)->first();
            if ($gallery) {
                $this->tempImages = collect($gallery->images)->sortBy('order')->values()->toArray();
            } else {
                $this->tempImages = [];
            }
        } else {
            $this->tempImages = [];
        }

        $this->syncMemory();
    }

    /**
     * Sauvegarde l'état du tableau RAM vers le Cache serveur avec péremption d'1 Heure
     * Le cache est utilisé car la requête du formulaire principal du Post sera asynchrone par rapport à ce composant.
     */
    private function syncMemory()
    {
        \Illuminate\Support\Facades\Cache::put("gallery_temp_{$this->sessionToken}", $this->tempImages, 3600);
    }

    public function updatedNewImages()
    {
        $this->validate();
        $this->saveNewImages();
    }

    private function saveNewImages()
    {
        if (empty($this->newImages)) return;

        $manager = new ImageManager(new Driver());
        // Les nouveaux ajouts sont systématiquement isolés en brouillon
        $folderName = "temp_{$this->sessionToken}";

        foreach ($this->newImages as $image) {
            $filename = time() . '_' . $image->getClientOriginalName();
            
            $path = $image->storeAs("galleries/{$folderName}", $filename, 'public');
            $fullPath = storage_path('app/public/' . $path);

            $img = $manager->read($fullPath);
            $img->scaleDown(1024, 1024);
            $img->save($fullPath, quality: 80);

            // Stockage de la vignette en RAM locale
            $this->tempImages[] = [
                'path' => '/storage/' . $path,
                'filename' => $image->getClientOriginalName(),
                'order' => count($this->tempImages) + 1,
            ];
        }

        $this->syncMemory();
        $this->newImages = []; // Flush temp files
    }

    public function removeImage(string $path)
    {
        // Nettoyage disque immédiat SEULEMENT SI le fichier est dans le brouillon actuel !
        if (str_contains($path, "temp_{$this->sessionToken}")) {
            $storagePath = str_replace('/storage/', '', $path);
            Storage::disk('public')->delete($storagePath);
        }

        // Pour les images préexistantes ou brouillon, retrait de l'état mémoire
        $images = collect($this->tempImages);
        $this->tempImages = $images
            ->reject(fn ($img) => $img['path'] === $path)
            ->values()
            ->map(fn ($img, $index) => array_merge($img, ['order' => $index + 1]))
            ->toArray();

        $this->syncMemory();
    }

    public function moveUp(int $currentOrder)
    {
        $this->reorder($currentOrder, $currentOrder - 1);
    }

    public function moveDown(int $currentOrder)
    {
        $this->reorder($currentOrder, $currentOrder + 1);
    }

    private function reorder(int $from, int $to)
    {
        $images = collect($this->tempImages)->sortBy('order')->values();

        $fromIndex = $from - 1;
        $toIndex = $to - 1;

        if ($toIndex < 0 || $toIndex >= $images->count()) return;

        $temp = $images[$fromIndex];
        $images[$fromIndex] = $images[$toIndex];
        $images[$toIndex] = $temp;

        $this->tempImages = $images->map(fn ($img, $i) => array_merge($img, ['order' => $i + 1]))->toArray();
        $this->syncMemory();
    }

    /**
     * PROCESSUS DE SYNCHRONISATION GLOBAL DÉCLENCHÉ DEPUIS LE BOUTON 'ENREGISTRER' DU PARENT
     * Réconcilie l'état mis en Cache au niveau physique et SQL.
     */
    public static function processTempGallery($postId, $sessionToken)
    {
        if (!$sessionToken) return;
        $tempImages = \Illuminate\Support\Facades\Cache::get("gallery_temp_{$sessionToken}");
        
        // S'il n'y a pas d'état en cache parcequ'aucune instance Galerie n'était ouverte
        // ou cas d'expiration très long
        if ($tempImages === null) return;

        $gallery = Gallery::firstOrCreate(['post_id' => $postId], ['images' => []]);
        $existingPaths = collect($gallery->images)->pluck('path')->toArray();
        $newPaths = collect($tempImages)->pluck('path')->toArray();

        // 1. Suppression physique des vieilles images évacuées de l'état
        $deletedPaths = array_diff($existingPaths, $newPaths);
        foreach ($deletedPaths as $dPath) {
             Storage::disk('public')->delete(str_replace('/storage/', '', $dPath));
        }

        // 2. Déplacement physique et réécriture chemins des nouvelles images brouillon
        $finalImages = array_map(function ($img) use ($postId, $sessionToken) {
            if (str_contains($img['path'], "galleries/temp_{$sessionToken}")) {
                $oldStorage = str_replace('/storage/', '', $img['path']);
                $newStorage = str_replace("galleries/temp_{$sessionToken}", "galleries/{$postId}", $oldStorage);
                
                if (!Storage::disk('public')->exists("galleries/{$postId}")) {
                    Storage::disk('public')->makeDirectory("galleries/{$postId}");
                }
                if (Storage::disk('public')->exists($oldStorage)) {
                    Storage::disk('public')->move($oldStorage, $newStorage);
                }
                
                return [
                    'path' => '/storage/' . $newStorage,
                    'filename' => $img['filename'],
                    'order' => $img['order'],
                ];
            }
            return $img;
        }, $tempImages);

        // 3. Persistance de la vérité SQL
        $gallery->update(['images' => $finalImages]);

        // Nettoyage hygiénique du résidu temp_XYZ
        if (Storage::disk('public')->exists("galleries/temp_{$sessionToken}")) {
            Storage::disk('public')->deleteDirectory("galleries/temp_{$sessionToken}");
        }

        // Vider la galerie si elle est à sec pour ne pas encombrer les requêtes DB
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
            'hasImages' => count($images) > 0,
            'imagesList' => $images,
        ]);
    }
}
