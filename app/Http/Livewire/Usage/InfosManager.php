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
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.infos-manager', [
            'favoritesPosts' => $this->user
                ->favoritePosts()
                ->paginate($this->perPage),
        ]);
    }
}
