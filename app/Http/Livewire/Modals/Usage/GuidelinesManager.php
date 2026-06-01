<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;

/**
 * Composant Livewire pour l'affichage des guides et directives d'utilisation.
 */
class GuidelinesManager extends Component
{
    /**
     * Sujet des directives ou guide (ex: 'welcome').
     *
     * @var string
     */
    public $subject;

    /**
     * Données associées aux directives.
     *
     * @var array
     */
    public $data;

    /**
     * Initialisation du composant avec les données du sac modal.
     *
     * @param array $modalBag Sac de données contenant le sujet et les données.
     */
    public function mount($modalBag) {
        $this->subject= $modalBag['subject'] ?? 'welcome';
        $this->data= $modalBag['data'] ?? [];
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.usage.guidelines-manager');
    }
}
