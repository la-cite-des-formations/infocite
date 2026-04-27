<?php

namespace App\Http\Livewire\Modals\Admin\Formats;

use App\Models\Format;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de mises en forme.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des mises en forme à supprimer.
     *
     * @var array
     */
    public $formatsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $formatsIDs Identifiants des mises en forme à supprimer.
     */
    public function mount($formatsIDs) {
        $this->formatsIDs = $formatsIDs;
    }

    /**
     * Supprime les mises en forme sélectionnées de la base de données.
     */
    public function delete() {
        Format::whereIn('id', $this->formatsIDs)->delete();

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
            'headerModelsList' => count($this->formatsIDs) > 1 ? 'Mises en forme concernées' : 'Mise en forme concernée',
            'models' => Format::query()
                ->whereIn('id', $this->formatsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
