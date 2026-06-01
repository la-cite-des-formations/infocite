<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;
use App\Models\Post;
use App\Models\Rubric;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Composant Livewire pour la gestion des favoris (articles et rubriques) côté usage.
 */
class FavorisManager extends Component
{
    use WithPagination;
    use WithModal;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;

    /**
     * Rubrique actuelle.
     *
     * @var \App\Models\Rubric
     */
    public $rubric;

    protected $paginationTheme = 'bootstrap';
    /**
     * Options de nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [12, 24, 36, 48, 60];
    /**
     * Nombre d'éléments par page sélectionné.
     *
     * @var int
     */
    public $perPage;

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
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant la rubrique.
     */
    public function mount($viewBag) {
        session([
            'backRoute' => request()->getRequestUri(),
            'appsBackRoute' => request()->getRequestUri(),
        ]);
        $this->perPage = session('postsPerPage', 12);
        $this->rubric = $viewBag->rubric;
        $this->setMode();
        $this->setNotifications();

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
    public function redirectToPost($postId) {
        if (!$this->blockRedirection) {
            redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
        }
        $this->blockRedirection = FALSE;
    }

    /**
     * Retire une rubrique des favoris.
     *
     * @param int $rubric_id Identifiant de la rubrique.
     */
    public function removeFavoriteRubric($rubric_id) {
        $this->rubric = Rubric::find($rubric_id);

        $this->rubric->toggleFavorite();

        $this->emitSelf('render');
    }

    /**
     * Bloque la redirection automatique.
     */
    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->rendered = TRUE;
        $user = User::find(auth()->user()->id);
        $favoritesPosts = $user->favoritePosts()->paginate($this->perPage);
        $favoritesRubrics = $user->favoriteRubrics;

        return view('livewire.usage.favoris-manager',[
            'posts' => $favoritesPosts,
            'rubrics' => $favoritesRubrics,
        ]);
    }
}
