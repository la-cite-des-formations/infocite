<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Rubric;
use App\Models\User;

/**
 * Composant Livewire pour la gestion des commentaires dans l'interface d'administration.
 */
class CommentsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'comments';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'comments';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les commentaires.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => FALSE,
        'search' => '',
        'authorId' => '',
        'rubricId' => '',
        'postId' => '',
    ];

    /**
     * Options pour le nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [10, 15, 25];

    /**
     * Nombre d'éléments par page sélectionné.
     *
     * @var int
     */
    public $perPage = 10;

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'comments' => Comment::filter($this->filter)
                ->orderByRaw('user_id, created_at DESC')
                ->paginate($this->perPage),
            'authors' => User::allWho('have-commented-posts'),
            'rubrics' => Rubric::allWithPosts(),
            'posts' => Post::allCommentable()
                ->when($this->filter['rubricId'], function ($query) {
                    $query->where('rubric_id', $this->filter['rubricId']);
                })
                ->get(),
            ]);
    }
}
