<?php

namespace App\Http\Livewire\Modals;

use Livewire\Component;

/**
 * Modale de sélection du point focal pour la première image de galerie.
 * Reçoit l'URL de l'image et les coordonnées courantes, émet focalPointUpdated.
 */
class FocalPointPicker extends Component
{
    /** URL publique de l'image à cadrer. */
    public string $imagePath = '';

    /** Coordonnées courantes du point focal (0–100). */
    public float $focalX = 50.0;
    public float $focalY = 50.0;

    /** Métadonnées de l'article pour l'aperçu simulé. */
    public string $postTitle   = '';
    public string $postIcon    = '';
    public string $postExcerpt = '';

    public function mount($data = [], $filter = null): void
    {
        $this->imagePath   = $data['imagePath']   ?? '';
        $this->focalX      = (float) ($data['focalX']      ?? 50.0);
        $this->focalY      = (float) ($data['focalY']      ?? 50.0);
        $this->postTitle   = $data['postTitle']   ?? '';
        $this->postIcon    = $data['postIcon']    ?? '';
        $this->postExcerpt = $data['postExcerpt'] ?? '';
    }

    protected $listeners = [
        'focalCoordsChanged' => 'updateCoords',
    ];

    /**
     * Reçoit les coordonnées calculées côté JS via l'event bus Livewire.
     */
    public function updateCoords(float $x, float $y): void
    {
        $this->focalX = round(max(0, min(100, $x)), 2);
        $this->focalY = round(max(0, min(100, $y)), 2);
    }

    /**
     * Valide et transmet le point focal au composant PostGallery puis ferme la modale.
     */
    public function confirm(): void
    {
        $this->emitTo('usage.post-gallery', 'focalPointUpdated', $this->focalX, $this->focalY);
        $this->emit('modalClosed');
    }

    public function render()
    {
        return view('livewire.modals.focal-point-picker');
    }
}
