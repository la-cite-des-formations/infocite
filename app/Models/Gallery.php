<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Représente une galerie de photos associée à un article (relation 1:1).
 * Les images sont stockées sous forme de tableau JSON [{path, filename, order}].
 */
class Gallery extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['post_id', 'images'];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array',
    ];

    /**
     * Relation vers l'article parent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Retourne les images triées par ordre.
     *
     * @return \Illuminate\Support\Collection
     */
    public function sortedImages()
    {
        return collect($this->images)->sortBy('order')->values();
    }

    /**
     * Retourne le nombre d'images dans la galerie.
     *
     * @return int
     */
    public function imagesCount(): int
    {
        return count($this->images ?? []);
    }

    /**
     * Ajoute une image à la galerie.
     *
     * @param string $path  Chemin public de l'image.
     * @param string $filename  Nom original du fichier.
     * @return void
     */
    public function addImage(string $path, string $filename): void
    {
        $images = $this->images ?? [];
        $maxOrder = collect($images)->max('order') ?? 0;

        $images[] = [
            'path'     => $path,
            'filename' => $filename,
            'order'    => $maxOrder + 1,
        ];

        $this->images = $images;
        $this->save();
    }

    /**
     * Retire une image de la galerie par son chemin et supprime le fichier physique.
     *
     * @param string $path  Chemin de l'image à retirer.
     * @return void
     */
    public function removeImage(string $path): void
    {
        $images = collect($this->images ?? []);

        // Suppression du fichier physique sur le disk public
        $storagePath = str_replace('/storage/', '', $path);
        Storage::disk('public')->delete($storagePath);

        // Retrait de l'image du tableau et ré-indexation de l'ordre
        $newImages = $images
            ->reject(fn ($img) => $img['path'] === $path)
            ->values()
            ->map(fn ($img, $index) => array_merge($img, ['order' => $index + 1]))
            ->toArray();

        $this->update(['images' => $newImages]);
    }

    /**
     * Supprime l'ensemble des fichiers physiques de la galerie.
     * À appeler avant la suppression du modèle.
     *
     * @return void
     */
    public function deleteAllFiles(): void
    {
        foreach ($this->images ?? [] as $image) {
            $storagePath = str_replace('/storage/', '', $image['path']);
            Storage::disk('public')->delete($storagePath);
        }
    }

    /**
     * Événement de suppression : nettoie les fichiers physiques et le répertoire.
     */
    protected static function booted()
    {
        static::deleting(function (Gallery $gallery) {
            $gallery->deleteAllFiles();

            // Suppression du répertoire dédié à l'article
            $dir = "galleries/{$gallery->post_id}";
            if (Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });
    }
}
