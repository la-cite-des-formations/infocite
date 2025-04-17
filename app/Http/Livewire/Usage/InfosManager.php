<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use Livewire\Component;
use App\Models\Post;
use App\Models\Rubric;
use App\Http\Livewire\WithUsageMode;
use Livewire\WithPagination;

class InfosManager extends Component
{
    use WithUsageMode;
    use WithPagination;
    use WithFavoritesHandling;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['updatePermission', 'refreshPermissionSwitch' => 'loadUser'];

    public $user;
    public $rubric;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $perPageOptions = [12, 24, 36, 48, 60];
    public $perPage;
    public $blockRedirection = FALSE;

    protected $rules = [
        'user.desktop_notifications_granted' => 'required',
        'user.notify_only_favorites' => 'required',
    ];

    public function loadUser() {
        $this->user = auth()->user();
    }

    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);

        $this->loadUser();
        $this->perPage = session('favoritesPostsPerPage', 12);
        $this->setMode();
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    public function updatedPerPage() {
        session(['favoritesPostsPerPage' => $this->perPage]);
        $this->resetPage();
    }

    public function redirectToPost($postId) {
        if (!$this->blockRedirection) {
            redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
        }
        $this->blockRedirection = FALSE;
    }

    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    public function updatedUserNotifyOnlyFavorites() {
        $this->user->update();
    }

    public function updatePermission($permission) {
        $this->user->update(['desktop_notifications_granted' => $permission !== 'denied']);

        if ($permission === 'default') {
            redirect()->to($this->rubric->segment);
        }
    }

    public function updatedUserDesktopNotificationsGranted() {
        if ($this->user->desktop_notifications_granted) {
            $this->emit('verifyPermission');
        }
        else {
            $this->user->update();
        }
    }

    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.infos-manager', [
            'favoritesPosts' => $this->user
                ->myFavoritesPosts()
                ->paginate($this->perPage),
        ]);
    }
}
