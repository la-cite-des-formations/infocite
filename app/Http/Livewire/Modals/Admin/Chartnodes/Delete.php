<?php

namespace App\Http\Livewire\Modals\Admin\Chartnodes;

use App\Models\Chartnode;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de nœuds graphiques.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des nœuds graphiques à supprimer.
     *
     * @var array
     */
    public $chartnodessIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $chartnodessIDs Identifiants des nœuds graphiques à supprimer.
     */
    public function mount($chartnodessIDs) {
        $this->chartnodessIDs = $chartnodessIDs;
    }

    /**
     * Supprime les nœuds graphiques sélectionnés de la base de données.
     */
    public function delete() {
        Chartnode::whereIn('id', $this->chartnodessIDs)->delete();

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
            'headerModelsList' => count($this->chartnodessIDs) > 1 ? 'Noeuds graphiques concernés' : 'Noeud graphique concerné',
            'models' => Chartnode::query()
                ->whereIn('id', $this->chartnodessIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
