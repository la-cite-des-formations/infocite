<?php

namespace App\Http\Livewire\Modals\Admin\Comments;

use App\Models\Comment;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de commentaires.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des commentaires à supprimer.
     *
     * @var array
     */
    public $commentsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $commentsIDs Identifiants des commentaires à supprimer.
     */
    public function mount($commentsIDs) {
        $this->commentsIDs = $commentsIDs;
    }

    /**
     * Supprime les commentaires sélectionnés.
     */
    public function delete() {
        Comment::whereIn('id', $this->commentsIDs)->delete();

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
            'headerModelsList' => count($this->commentsIDs) > 1 ? 'Commentaires concernés' : 'Commentaire concerné',
            'models' => Comment::query()
                ->whereIn('id', $this->commentsIDs)
                ->orderByRaw('user_id, created_at DESC')
                ->get(),
            'modelInfo' => ['field' => 'content'],
        ]);
    }
}
