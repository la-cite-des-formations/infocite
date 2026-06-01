<?php

namespace App\Http\Livewire\Modals\Admin\Apps;

use App\Models\App;
use App\Models\Right;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression d'applications.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des applications à supprimer.
     *
     * @var array
     */
    public $appsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $appsIDs Identifiants des applications à supprimer.
     */
    public function mount($appsIDs) {
        $this->appsIDs = $appsIDs;
    }

    /**
     * Supprime les applications sélectionnées ainsi que leurs droits associés.
     */
    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'App')
                ->whereIn('resource_id', $this->appsIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'App')
                ->whereIn('resource_id', $this->appsIDs)
                ->delete();
        });
        App::whereIn('id', $this->appsIDs)->delete();

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
            'headerModelsList' => count($this->appsIDs) > 1 ? 'Applications concernées' : 'Application concernée',
            'models' => App::query()
                ->whereIn('id', $this->appsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
