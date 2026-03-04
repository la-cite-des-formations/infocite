<?php

namespace App\Http\Livewire\Modals\Admin\Groups;

use App\Models\Group;
use App\Models\Right;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de groupes.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des groupes à supprimer.
     *
     * @var array
     */
    public $groupsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $groupsIDs Identifiants des groupes à supprimer.
     */
    public function mount($groupsIDs) {
        $this->groupsIDs = $groupsIDs;
    }

    /**
     * Supprime les groupes sélectionnés ainsi que leurs droits associés.
     */
    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'Group')
                ->whereIn('resource_id', $this->groupsIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'Group')
                ->whereIn('resource_id', $this->groupsIDs)
                ->delete();
        });
        Group::whereIn('id', $this->groupsIDs)->delete();

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
            'headerModelsList' => count($this->groupsIDs) > 1 ? 'Groupes concernés' : 'Groupe concerné',
            'models' => Group::query()
                ->whereIn('id', $this->groupsIDs)
                ->orderByRaw('type ASC, name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
