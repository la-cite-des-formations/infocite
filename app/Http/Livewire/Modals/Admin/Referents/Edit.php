<?php

namespace App\Http\Livewire\Modals\Admin\Referents;

use App\Models\User;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'affichage des détails d'un référent.
 */
class Edit extends Component
{
    /**
     * Modèle de l'utilisateur référent.
     *
     * @var \App\Models\User
     */
    public $referent;

    /**
     * Définit le référent à afficher.
     *
     * @param int|null $id Identifiant de l'utilisateur.
     */
    public function setReferent($id = NULL) {
        $this->referent = $this->referent ?? User::findOrNew($id);
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de l'utilisateur concerné.
     */
    public function mount($data) {
        extract($data);

        $this->setReferent($id ?? NULL);
    }


    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render(){
        return view('livewire.modals.admin.referents.sheet');
    }
}
