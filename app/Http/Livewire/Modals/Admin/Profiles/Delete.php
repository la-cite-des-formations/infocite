<?php

namespace App\Http\Livewire\Modals\Admin\Profiles;

use App\Models\Right;
use App\Models\User;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de profils.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des profils à supprimer.
     *
     * @var array
     */
    public $profilesIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $profilesIDs Identifiants des profils à supprimer.
     */
    public function mount($profilesIDs) {
        $this->profilesIDs = $profilesIDs;
    }

    /**
     * Supprime les profils sélectionnés ainsi que leurs droits associés.
     */
    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->profilesIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->profilesIDs)
                ->delete();
        });
        User::whereIn('id', $this->profilesIDs)->delete();

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
            'headerModelsList' => count($this->profilesIDs) > 1 ? 'Profils concernés' : 'Profil concerné',
            'models' => User::query()
                ->whereIn('id', $this->profilesIDs)
                ->orderByRaw('first_name ASC')
                ->get(),
            'modelInfo' => ['field' => 'first_name'],
        ]);
    }
}
