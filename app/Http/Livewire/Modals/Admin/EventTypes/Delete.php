<?php

namespace App\Http\Livewire\Modals\Admin\EventTypes;

use App\Models\EventType;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de types d'événements.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des types d'événements à supprimer.
     *
     * @var array
     */
    public $eventTypesIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $eventTypesIDs Identifiants à supprimer.
     */
    public function mount($eventTypesIDs) {
        $this->eventTypesIDs = $eventTypesIDs;
    }

    /**
     * Supprime les types d'événements sélectionnés.
     */
    public function delete() {
        try {
            EventType::whereIn('id', $this->eventTypesIDs)->delete();
            $this->deletionPerformed = TRUE;
        } catch (\Exception $e) {
            $this->addError('deletion', "Impossible de supprimer ce type d'événement car il est associé à un ou plusieurs événements existants.");
        }
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->eventTypesIDs) > 1 ? "Types d'événements concernés" : "Type d'événement concerné",
            'models' => EventType::query()
                ->whereIn('id', $this->eventTypesIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
