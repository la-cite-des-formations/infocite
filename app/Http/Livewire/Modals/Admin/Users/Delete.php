<?php

namespace App\Http\Livewire\Modals\Admin\Users;

use App\Models\Right;
use App\Models\User;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression d'utilisateurs.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des utilisateurs à supprimer.
     *
     * @var array
     */
    public $usersIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $usersIDs Identifiants des utilisateurs à supprimer.
     */
    public function mount($usersIDs) {
        $this->usersIDs = $usersIDs;
    }

    /**
     * Supprime les utilisateurs sélectionnés ainsi que leurs droits associés.
     */
    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->usersIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->usersIDs)
                ->delete();
        });
        User::whereIn('id', $this->usersIDs)->delete();

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
            'headerModelsList' => count($this->usersIDs) > 1 ? 'Utilisateurs concernés' : 'Utilisateur concerné',
            'models' => User::query()
                ->whereIn('id', $this->usersIDs)
                ->orderByRaw('name ASC, first_name ASC')
                ->get(),
            'modelInfo' => ['function' => 'identity'],
        ]);
    }
}
