<?php

namespace App\Http\Livewire\Modals\Admin\Events;

use App\Models\Event;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression d'événements dans l'administration.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des événements à supprimer.
     *
     * @var array
     */
    public $eventsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $eventsIDs Identifiants des événements à supprimer.
     */
    public function mount($eventsIDs) {
        $this->eventsIDs = $eventsIDs;
    }

    /**
     * Supprime les événements sélectionnés.
     */
    public function delete() {
        Event::whereIn('id', $this->eventsIDs)->delete();
        $this->deletionPerformed = TRUE;
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->eventsIDs) > 1 ? 'Événements concernés' : 'Événement concerné',
            'models' => Event::query()
                ->whereIn('id', $this->eventsIDs)
                ->orderByRaw('start_date DESC, title ASC')
                ->get(),
            'modelInfo' => ['field' => 'title'],
        ]);
    }
}
