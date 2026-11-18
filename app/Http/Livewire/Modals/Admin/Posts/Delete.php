<?php

namespace App\Http\Livewire\Modals\Admin\Posts;

use App\Models\Event;
use App\Models\Post;
use App\Models\Right;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression d'articles.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des articles à supprimer.
     *
     * @var array
     */
    public $postsIDs;

    /**
     * Indique si les événements liés doivent également être supprimés.
     *
     * @var bool
     */
    public $deleteLinkedEvents = false;

    /**
     * Nombre d'événements liés concernés.
     *
     * @var int
     */
    public $linkedEventsCount = 0;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Initialisation du composant.
     *
     * @param array $postsIDs Identifiants des articles à supprimer.
     */
    public function mount($postsIDs) {
        $this->postsIDs = $postsIDs;
        $this->linkedEventsCount = Event::whereIn('post_id', $this->postsIDs)->count();
    }

    /**
     * Supprime les articles sélectionnés ainsi que leurs droits associés.
     */
    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'Post')
                ->whereIn('resource_id', $this->postsIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'Post')
                ->whereIn('resource_id', $this->postsIDs)
                ->delete();
        });

        if ($this->deleteLinkedEvents) {
            Event::whereIn('post_id', $this->postsIDs)->delete();
        } else {
            Event::whereIn('post_id', $this->postsIDs)->update(['post_id' => null]);
        }

        Post::whereIn('id', $this->postsIDs)->delete();

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
            'headerModelsList' => count($this->postsIDs) > 1 ? 'Contenus concernés' : 'Contenu concerné',
            'models' => Post::query()
                ->whereIn('id', $this->postsIDs)
                ->orderByRaw('rubric_id, title ASC')
                ->get(),
            'modelInfo' => ['field' => 'title'],
        ]);
    }
}
