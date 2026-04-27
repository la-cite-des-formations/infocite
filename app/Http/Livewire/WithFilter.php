<?php

namespace App\Http\Livewire;

/**
 * Trait de base pour la gestion de l'affichage du menu de filtrage et la réinitialisation de la pagination.
 */
trait WithFilter
{
    /**
     * État d'affichage du filtre.
     *
     * @var bool
     */
    public $showFilter = FALSE;

    /**
     * Se déclenche lors de la mise à jour des filtres.
     */
    /**
     * Se déclenche lors de la mise à jour des filtres et réinitialise la pagination.
     */
    public function updatedWithFilter()
    {
        $this->resetPage();
    }

    /**
     * Alterne l'affichage du menu de filtre.
     */
    public function toggleFilter() {
        $this->showFilter = !$this->showFilter;
    }
}
