<?php

namespace App\Http\Livewire\Usage;

use App\Models\App;
use App\Models\Post;
use App\Models\Rubric;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithModal;

/**
 * Composant Livewire pour l'affichage des résultats de recherche globale.
 */
class SearchResultManager extends Component
{
    use WithPagination;
    use WithModal;

    /**
     * Rubrique courante.
     *
     * @var \App\Models\Rubric
     */
    public $rubric;

    /**
     * Indique si le composant a été rendu.
     *
     * @var bool
     */
    public $rendered = FALSE;

    /**
     * Indique si c'est le premier chargement.
     *
     * @var bool
     */
    public $firstLoad = TRUE;

    /**
     * Indique si la redirection est bloquée.
     *
     * @var bool
     */
    public $blockRedirection = FALSE;

    /**
     * Chaîne de caractères recherchée.
     *
     * @var string
     */
    public $searchedStr;

    /**
     * Thème de pagination utilisé.
     *
     * @var string
     */
    protected $paginationTheme = 'bootstrap';

    /**
     * Options pour le nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [8, 10, 25];

    /**
     * Nombre d'articles par page.
     *
     * @var int
     */
    public $postsPerPage;

    /**
     * Nombre d'applications par page.
     *
     * @var int
     */
    public $appsPerPage;


    /**
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant le segment de la rubrique.
     */
    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->postsPerPage = session('searchResultPostsPerPage', 8);
        $this->appsPerPage = session('searchResultAppsPerPage', 8);
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->searchedStr = request()->input('searchedStr');
    }

    /**
     * Détermine si c'est le premier chargement du composant.
     */
    public function booted()
    {
        $this->firstLoad = !$this->rendered;
    }

    /**
     * Redirige vers la page de l'article sélectionné.
     *
     * @param int $postId Identifiant de l'article.
     */
    public function redirectToPost($postId)
    {
        redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
    }

    /**
     * Redirige vers l'URL de l'application dans un nouvel onglet.
     *
     * @param string $appUrl URL cible.
     */
    public function redirectToApp($appUrl) {
        if (!$this->blockRedirection) {
            $this->emit('newTabRedirection', $appUrl);
        }
        else {
            $this->blockRedirection = FALSE;
        }
    }

    /**
     * Bloque la redirection automatique (utile pour les boutons d'action).
     */
    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    /**
     * Met à jour le nombre d'articles par page dans la session.
     */
    public function updatedPostsPerPage() {
        session(['searchResultPostsPerPage' => $this->postsPerPage]);

        $this->resetPage('foundPostsPage');
    }

    /**
     * Met à jour le nombre d'applications par page dans la session.
     */
    public function updatedAppsPerPage() {
        session(['searchResultAppsPerPage' => $this->appsPerPage]);

        $this->resetPage('foundAppsPage');
    }

    /**
     * Rendu du composant.
     * Effectue la recherche sur les articles et les applications.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.search-result-manager', [
            'foundPosts' => Post::query()
                ->whereIn('id', Post::query()
                    ->where('title', 'like', "%$this->searchedStr%")
                    ->orWhere('content', 'like', "%$this->searchedStr%")
                    ->get()
                    ->filter(function ($post) {
                        return auth()->user()->can('read', $post);
                    })
                    ->pluck('id')
                )
                ->orderByRaw('published_at DESC, title')
                ->paginate($this->postsPerPage, '*', 'foundPostsPage'),
            'foundApps' => App::query()
                ->whereIn('id', App::query()
                    ->where(function ($query) {
                        $query
                            ->whereNull('owner_id')
                            ->orWhere('owner_id', auth()->user()->id);
                    })
                    ->where(function ($query) {
                        $query
                            ->where('name', 'like', "%$this->searchedStr%")
                            ->orWhere('description', 'like', "%$this->searchedStr%")
                            ->orWhere('url', 'like', "%$this->searchedStr%");
                    })
                    ->get()
                    ->filter(function ($app) {
                        return auth()->user()->can('view', $app);
                    })
                    ->pluck('id')
                )
                ->orderBy('name')
                ->paginate($this->appsPerPage, '*', 'foundAppsPage'),
            'replaceStr' => '/'. $this->searchedStr . '/i',
        ]);
    }
}
