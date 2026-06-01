<?php

namespace App\Http\Livewire\Modals\Admin\Comments;

use App\Models\Comment;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'affichage des détails d'un commentaire.
 */
class Edit extends Component
{
    /**
     * Modèle du commentaire.
     *
     * @var \App\Models\Comment
     */
    public $comment;

    /**
     * Définit le commentaire à afficher.
     *
     * @param int|null $id Identifiant du commentaire.
     */
    public function setComment($id = NULL) {
        $this->comment = $this->comment ?? Comment::findOrNew($id);
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID du commentaire concerné.
     */
    public function mount($data) {
        extract($data);

        $this->setComment($id ?? NULL);
    }


    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render(){
        return view('livewire.modals.admin.comments.sheet');
    }
}
