<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithFilterPosts;
use App\Http\Livewire\WithPinnedHandling;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;

/**
 * Composant Livewire pour la navigation et l'affichage des articles d'une rubrique.
 */
class PostsManager extends Component
{
    use WithPagination;
    use WithModal;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;
    use WithPinnedHandling;
    use WithFilterPosts;

    /**
     * Thème de pagination.
     *
     * @var string
     */
    protected $paginationTheme = 'bootstrap';

    /**
     * Options de nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [12, 24, 36, 48, 60];

    /**
     * Nombre d'éléments par page courant.
     *
     * @var int
     */
    public $perPage = 12;

    /**
     * Rubrique courante.
     *
     * @var \App\Models\Rubric
     */
    public $rubric;

    /**
     * Liste des articles (chargée dynamiquement).
     *
     * @var \Illuminate\Pagination\LengthAwarePaginator
     */
    protected $posts;

    /**
     * Indique si la rubrique est en favoris.
     *
     * @var bool
     */
    public $isFavoriteRubric;

    /**
     * Liste des 4 articles les plus récents pour la Une.
     *
     * @var \Illuminate\Support\Collection
     */
    public $recentPosts;

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


    protected $listeners = ['modalClosed', 'deletePost', 'render'];

    /**
     * Filtres actifs pour la liste d'articles.
     *
     * @var array
     */
    public $filter = [
        'favoritePosts' => '',
        'notViewPosts' => '',
        'postsInFavoritesRubrics' => '',
        'allPosts' => '',
    ];
    /**
     * Tris actifs pour la liste d'articles.
     *
     * @var array
     */
    public $sorter = [
        'mostConsultedPosts' => '',
        'mostRecentlyPosts' => '',
        'mostCommentedPosts' => '',
    ];

    /**
     * Initialisation du composant.
     * Configure la pagination, le mode d'usage, la rubrique et les notifications.
     *
     * @param object $viewBag Sac de données contenant la rubrique.
     */
    public function mount($viewBag)
    {
        session([
            'backRoute' => request()->getRequestUri(),
            'appsBackRoute' => request()->getRequestUri(),
        ]);

        $this->perPage = session('postsPerPage', 12);
        $this->setMode();
        $this->rubric = $viewBag->rubric;
        $this->isFavoriteRubric = $this->rubric->isFavorite;
        $this->recentPosts = collect();
        $this->setNotifications();
        $this->lastFilterActive();
        $this->lastSorterActive();
    }

    /**
     * Fonction appelée après le rendu du composant.
     */
    public function booted()
    {
        $this->firstLoad = !$this->rendered;
    }

    /**
     * Met à jour le nombre d'articles par page dans la session.
     */
    public function updatedPerPage()
    {
        session(['postsPerPage' => $this->perPage]);
        $this->resetPage();
    }

    /**
     * Supprime un article.
     *
     * @param int $postId Identifiant de l'article.
     */
    public function deletePost($postId)
    {
        Post::find($postId)->delete();
    }

    /**
     * Redirige vers la page de l'article sélectionné.
     *
     * @param int $postId Identifiant de l'article.
     */
    public function redirectToPost($postId)
    {
        if (!$this->blockRedirection) {
            redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
        }
        $this->blockRedirection = FALSE;
    }

    /**
     * Bloque la redirection automatique.
     */
    public function blockRedirection()
    {
        $this->blockRedirection = TRUE;
    }

    /**
     * Bascule l'affichage des articles en mode liste.
     */
    public function displayListPosts()
    {
        session(['displayPosts'=>'list']);
        $this->resetPage();
        //Modification de l'affichage des applications
        $this->emit('displayUpdated');
    }

    /**
     * Bascule l'affichage des articles en mode grille.
     */
    public function displayGridPosts()
    {
        session(['displayPosts'=>'grid']);
        $this->resetPage();
        //Modification de l'affichage des applications
        $this->emit('displayUpdated');
    }

    /**
     * Récupère la liste de tous les articles de la rubrique courante (ou global selon le mode).
     * Gère les filtres de parution, d'archivage et de permissions.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function allPosts()
    {
        $user = auth()->user();
        $recentIds = $this->recentPosts ? $this->recentPosts->pluck('id')->toArray() : [];
        $myRubricsIds = $user->myRubrics()->pluck('id')->toArray();

        // Si l'utilisateur n'a accès à aucune rubrique, on s'arrête là
        if (empty($myRubricsIds)) {
            return Post::whereNull('id')->paginate($this->perPage);
        }

        // Construction de la requête de base avec filtrage par rubrique (SQL)
        $query = Post::query()
            ->notTemplates()
            ->where('is_pinned', FALSE);

        if ($this->rubric->name == 'Une' || $this->rubric->name == 'Archives') {
            $query->whereIn('rubric_id', $myRubricsIds);
        } else {
            $query->where('rubric_id', $this->rubric->id);
        }

        // Filtres de publication/archives (SQL)
        if ($this->rubric->name == 'Archives') {
            $query->where('published', TRUE)
                ->where('expired_at', '<=', today()->format('Y-m-d'))
                ->where('auto_delete', FALSE);
        } elseif ($this->mode != 'edition') {
            $query->where('published', TRUE)
                ->where(function ($q) {
                    $q->where('published_at', '<=', today()->format('Y-m-d'))
                      ->orWhereNull('published_at');
                })
                ->where(function ($q) {
                    $q->where('expired_at', '>', today()->format('Y-m-d'))
                      ->orWhereNull('expired_at');
                });
        }

        // On ne garde que les articles pour lesquels l'utilisateur a le droit de lecture
        // Au lieu de charger tous les articles, on filtre par politique si nécessaire
        // Mais pour optimiser, on peut essayer de paginer directement si le droit est global
        // Pour être sûr de respecter la politique complexe, on récupère uniquement les IDs
        $filteredIds = $query->select('id', 'rubric_id', 'author_id', 'published', 'published_at', 'expired_at')
            ->get()
            ->filter(fn($post) => $user->can('read', $post))
            ->pluck('id');

        return Post::query()
            ->with(['rubric', 'author', 'comments', 'currentUserReader', 'gallery'])
            ->whereIn('id', $filteredIds)
            ->when($this->rubric->name == 'Une' && $this->filter['allPosts'] == 'on', function ($q) use ($recentIds) {
                $q->whereNotIn('id', $recentIds);
            })
            ->orderByRaw('published_at DESC, updated_at DESC, created_at DESC')
            ->paginate($this->perPage);
    }

    /**
     * Retourne les articles filtrés ou triés en fonction de la session ou de la rubrique "Une".
     *
     * @return mixed
     */
    protected function getFilteredOrSortedPosts(){
        $user = auth()->user();
        
        // Chargement des 4 articles les plus récents pour la Une (uniquement si filtre par défaut)
        if ($this->rubric->name === 'Une') {
            $this->recentPosts = Post::query()
                ->notTemplates()
                ->with(['rubric', 'author', 'comments', 'currentUserReader', 'gallery'])
                ->whereIn('rubric_id', $user->myRubrics()->pluck('id'))
                ->where('published', TRUE)
                ->where(function ($query) {
                    $query->where('published_at', '<=', today()->format('Y-m-d'))
                          ->orWhereNull('published_at');
                })
                ->where('is_pinned', FALSE) // On ne veut pas de doublons avec les épinglés
                ->orderByRaw('published_at DESC, updated_at DESC, created_at DESC')
                ->take(4)
                ->get()
                ->filter(fn($post) => $user->can('read', $post));
        } else {
            $this->recentPosts = collect();
        }

        if (session()->has('lastFilter') && $this->rubric->name === 'Une'){
            return $this->lastFilterActive();
        }elseif (session()->has('lastSorter') && $this->rubric->name === 'Une'){
            return $this->lastSorterActive();
        }else{
            return $this->allPosts();
        }
    }

    /**
     * Récupère les modèles d'articles pertinents pour la rubrique actuelle.
     * Les modèles ne sont affichés qu'en mode édition et hors archives.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTemplates() {
        if ($this->mode != 'edition' || $this->rubric->name == 'Archives') {
            return collect();
        }

        $user = auth()->user();
        $myRubricsIds = $user->myRubrics()->pluck('id')->toArray();

        return Post::templates()
            ->with(['rubric', 'author', 'currentUserInteractions', 'currentUserReader'])
            ->when($this->rubric->name == 'Une', function ($query) {
                $query->whereNull('rubric_id');
            })
            ->when($this->rubric->name != 'Une', function ($query) {
                $query->where('rubric_id', $this->rubric->id);
            })
            ->where(function ($query) use ($myRubricsIds) {
                $query->whereIn('rubric_id', $myRubricsIds)
                    ->orWhereNull('rubric_id');
            })
            ->orderBy('title')
            ->get();
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->rendered = TRUE;
        return view('livewire.usage.posts-manager', [
            'posts' =>$this->getFilteredOrSortedPosts(),
            'pinnedPost' => $this->pinnedPosts(),
            'templates' => $this->getTemplates(),
        ]);
    }


}
