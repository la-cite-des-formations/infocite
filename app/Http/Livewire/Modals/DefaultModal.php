<?php

namespace App\Http\Livewire\Modals;

use Livewire\Component;

/**
 * Composant Livewire pour une modale par défaut vide.
 */
class DefaultModal extends Component
{
    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.default-modal');
    }
}
