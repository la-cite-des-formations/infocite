<?php

namespace App\Http\Livewire\Modals\Admin\Rights;

use App\Models\Right;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de droits.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des droits à supprimer.
     *
     * @var array
     */
    public $rightsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $rightsIDs Identifiants des droits à supprimer.
     */
    public function mount($rightsIDs) {
        $this->rightsIDs = $rightsIDs;
    }

    /**
     * Supprime les droits sélectionnés.
     */
    public function delete() {
        Right::whereIn('id', $this->rightsIDs)->delete();

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
            'headerModelsList' => count($this->rightsIDs) > 1 ? 'Droits concernés' : 'Droit concerné',
            'models' => Right::query()
                ->whereIn('id', $this->rightsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'description'],
        ]);
    }
}
