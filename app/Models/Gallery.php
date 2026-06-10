<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Représente une galerie de photos associée à un article (relation 1:1).
 *
 * Colonnes :
 *   - images      JSON  : tableau [{path, filename, order}]
 *   - focal_point JSON  : {x: float, y: float} — point de cadrage de la 1ère image (0–100)
 */
class Gallery extends Model
{
    protected $fillable = ['post_id', 'images', 'focal_point'];

    protected $casts = [
        'images'      => 'array',
        'focal_point' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Retourne les images triées par ordre.
     */
    public function sortedImages()
    {
        return collect($this->images ?? [])->sortBy('order')->values();
    }

    /**
     * Retourne le nombre d'images dans la galerie.
     */
    public function imagesCount(): int
    {
        return count($this->images ?? []);
    }

    /**
     * Retourne le point focal. Défaut : 50/50 (centre).
     *
     * @return array{x: float, y: float}
     */
    public function getFocalPoint(): array
    {
        $fp = $this->focal_point;
        return [
            'x' => isset($fp['x']) ? (float) $fp['x'] : 50.0,
            'y' => isset($fp['y']) ? (float) $fp['y'] : 50.0,
        ];
    }

    /**
     * Persiste le point focal.
     */
    public function setFocalPoint(float $x, float $y): void
    {
        $this->update([
            'focal_point' => [
                'x' => round(max(0, min(100, $x)), 2),
                'y' => round(max(0, min(100, $y)), 2),
            ]
        ]);
    }

    /**
     * Retourne la valeur CSS background-position prête à l'emploi.
     * Ex. "42.5% 30%"
     */
    public function focalPointCss(): string
    {
        $fp = $this->getFocalPoint();
        return "{$fp['x']}% {$fp['y']}%";
    }

    /**
     * Retire une image de la galerie par son chemin et supprime le fichier physique.
     */
    public function removeImage(string $path): void
    {
        Storage::disk('public')->delete(str_replace('/storage/', '', $path));

        $newImages = collect($this->images ?? [])
            ->reject(fn ($img) => $img['path'] === $path)
            ->values()
            ->map(fn ($img, $index) => array_merge($img, ['order' => $index + 1]))
            ->toArray();

        $this->update(['images' => $newImages]);
    }

    /**
     * Supprime l'ensemble des fichiers physiques de la galerie.
     */
    public function deleteAllFiles(): void
    {
        foreach ($this->images ?? [] as $image) {
            if (!isset($image['path'])) continue;
            Storage::disk('public')->delete(str_replace('/storage/', '', $image['path']));
        }
    }

    protected static function booted()
    {
        static::deleting(function (Gallery $gallery) {
            $gallery->deleteAllFiles();

            $dir = "galleries/{$gallery->post_id}";
            if (Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        });
    }
}
