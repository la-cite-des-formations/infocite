<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use Livewire\Component;
use App\Models\Post;
use App\Models\Rubric;
use App\Http\Livewire\WithUsageMode;
use Livewire\WithPagination;

/**
 * Composant Livewire pour l'affichage des informations personnelles et professionnelles de l'utilisateur.
 */
class InfosManager extends Component
{
    use WithUsageMode;
    use WithPagination;
    use WithFavoritesHandling;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['loadPermission', 'updatePermission', 'refreshPermissionSwitch' => 'loadUser'];

    /**
     * Utilisateur courant.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Profil employé de l'utilisateur.
     *
     * @var \App\Models\Employee
     */
    public $employee;

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
     * Options de nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [12, 24, 36, 48, 60];

    /**
     * Nombre d'éléments par page.
     *
     * @var int
     */
    public $perPage;

    /**
     * Indique si la redirection est bloquée.
     *
     * @var bool
     */
    public $blockRedirection = FALSE;

    /**
     * Indique si les notifications de bureau sont refusées par le navigateur.
     *
     * @var bool
     */
    public $browserDesktopNotificationsDenied = FALSE;

    /**
     * Filtre actif pour les articles à réviser ('all', 'author', 'corrector', 'orphan').
     *
     * @var string
     */
    public $reviewFilter = 'all';

    protected $rules = [
        'employee.desktop_notifications_granted' => 'required',
        'employee.notify_only_favorites' => 'required',
    ];

    /**
     * Charge les données de l'utilisateur et de son profil employé.
     *
     * @param string|null $permission Permission de notification (optionnel).
     */
    public function loadUser($permission = NULL) {
        $this->user = auth()->user();
        $this->employee = $this->user->employee;

        if ($permission) {
            $this->loadPermission($permission);
        }
    }

    /**
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant le segment de rubrique.
     */
    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);

        $this->perPage = session('favoritesPostsPerPage', 12);
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);

        $this->setMode();
        $this->loadUser();
    }

    /**
     * Détermine si c'est le premier chargement du composant.
     */
    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    /**
     * Met à jour le nombre d'éléments par page dans la session.
     */
    public function updatedPerPage() {
        session(['favoritesPostsPerPage' => $this->perPage]);
        $this->resetPage();
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
     * Bloque la redirection automatique (utile lors du clic sur un bouton d'action).
     */
    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    /**
     * Déclenche la mise à jour des préférences de notification.
     */
    public function updatedEmployeeNotifyOnlyFavorites() {
        $this->employee?->update();
    }

    /**
     * Met à jour l'indicateur de refus des notifications navigateur.
     *
     * @param string $permission État de la permission.
     */
    public function loadPermission($permission) {
        $this->browserDesktopNotificationsDenied = $permission === 'denied';
    }

    /**
     * Enregistre le changement de permission de notification pour l'utilisateur.
     *
     * @param string $permission État de la permission.
     */
    public function updatePermission($permission) {
        auth()->user()->employee?->update(['desktop_notifications_granted' => $permission !== 'denied']);

        if ($permission === 'default') {
            redirect()->to($this->rubric->route());
        }
    }

    /**
     * Vérifie la permission de notification lorsque l'utilisateur l'active.
     */
    public function updatedEmployeeDesktopNotificationsGranted() {
        if ($this->employee?->desktop_notifications_granted) {
            $this->emit('verifyPermission');
        }
        else {
            $this->employee?->update();
        }
    }

    /**
     * Confirme qu'un article obsolète est toujours d'actualité.
     * Réinitialise updated_at à la date du jour via touch(), ce qui sort l'article du scope pour 1 an.
     *
     * @param int $postId
     */
    public function confirmCurrent(int $postId) {
        $post = Post::findOrFail($postId);
        $this->authorizeReview($post);

        $post->touch();
        session()->flash('review_success', "L'article \"{$post->title}\" a été confirmé d'actualité pour 12 mois.");
    }

    /**
     * Archive un article obsolète.
     * Passe expired_at à aujourd'hui et auto_delete à 0 (retrait du flux public mais conservation en BDD).
     *
     * @param int $postId
     */
    public function archivePost(int $postId) {
        $post = Post::findOrFail($postId);
        $this->authorizeReview($post);

        $post->update([
            'expired_at'  => now(),
            'auto_delete' => false,
        ]);
        session()->flash('review_success', "L'article \"{$post->title}\" a été archivé (conservé hors ligne).");
    }

    /**
     * Supprime un article obsolète selon les droits de l'utilisateur.
     * Si l'utilisateur a le droit de suppression : delete immédiat.
     * Sinon : retrait immédiat du flux (expired_at = now) et auto_delete = 1 (purge prochaine par script Plesk).
     *
     * @param int $postId
     */
    public function deletePost(int $postId) {
        $post = Post::findOrFail($postId);
        $this->authorizeReview($post);

        if (auth()->user()->can('delete', $post)) {
            $post->delete();
            session()->flash('review_success', "L'article \"{$post->title}\" a été définitivement supprimé.");
        } else {
            $post->update([
                'expired_at'  => now(),
                'auto_delete' => true,
            ]);
            session()->flash('review_success', "L'article \"{$post->title}\" a été retiré de la publication et sera purgé prochainement.");
        }
    }

    /**
     * Vérifie que l'utilisateur courant est habilité à arbitrer sur cet article.
     *
     * @param \App\Models\Post $post
     */
    protected function authorizeReview(Post $post): void {
        abort_unless($post->isReviewableBy(auth()->user()), 403, "Action non autorisée sur cet article.");
    }

    /**
     * Définit le filtre actif pour la liste des articles à réviser.
     *
     * @param string $filter
     */
    public function setReviewFilter(string $filter): void {
        if (in_array($filter, ['all', 'author', 'corrector', 'orphan'])) {
            $this->reviewFilter = $filter;
        }
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        $this->rendered = TRUE;

        $allReviewPosts = Post::needsReview()
            ->with(['rubric'])
            ->get()
            ->filter(fn($p) => $p->isReviewableBy($this->user));

        // Si l'utilisateur n'a plus aucun article à réviser, auto-détacher les notifications 'RP'
        if ($allReviewPosts->isEmpty()) {
            $this->user->newNotifications()
                ->where('content_type', 'RP')
                ->detach();
            $this->user->unsetRelation('newNotifications');
        }

        // Compteurs par type de responsabilité
        $reviewCounts = [
            'all'       => $allReviewPosts->count(),
            'author'    => $allReviewPosts->filter(fn($p) => $p->author_id === $this->user->id)->count(),
            'corrector' => $allReviewPosts->filter(fn($p) => !$p->author_id && $p->corrector_id === $this->user->id)->count(),
            'orphan'    => $allReviewPosts->filter(fn($p) => !$p->author_id && !$p->corrector_id)->count(),
        ];

        // Application du filtre sélectionné
        $reviewPosts = match ($this->reviewFilter) {
            'author'    => $allReviewPosts->filter(fn($p) => $p->author_id === $this->user->id),
            'corrector' => $allReviewPosts->filter(fn($p) => !$p->author_id && $p->corrector_id === $this->user->id),
            'orphan'    => $allReviewPosts->filter(fn($p) => !$p->author_id && !$p->corrector_id),
            default     => $allReviewPosts,
        };

        return view('livewire.usage.infos-manager', [
            'favoritesPosts' => $this->user
                ->favoritePosts()
                ->paginate($this->perPage),
            'reviewPosts'    => $reviewPosts,
            'reviewCounts'   => $reviewCounts,
            'reviewFilter'   => $this->reviewFilter,
        ]);
    }
}
