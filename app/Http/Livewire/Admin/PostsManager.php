<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Post;
use App\Models\Rubric;
use App\Models\User;

/**
 * Composant Livewire pour la gestion des articles dans l'interface d'administration.
 * Permet le filtrage, la recherche et la pagination des articles.
 */
class PostsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'posts';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'posts';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche et de sélection d'articles.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => FALSE,
        'search' => '',
        'authorId' => '',
        'rubricId' => '',
        'phase' => '',
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
            'posts' => Post::filter($this->filter)
                ->orderByRaw('title ASC')
                ->paginate($this->perPage),
            'authors' => User::allWho('have-edited-posts'),
            'rubrics' => Rubric::allWithPosts(),
        ]);
    }
}
