<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use Livewire\Component;
use App\Post;
use App\Rubric;
use App\User;
use App\Http\Livewire\WithUsageMode;
use Livewire\WithPagination;


class InfosManager extends Component
{
    use WithUsageMode;
    use WithPagination;
    use WithFavoritesHandling;

    protected $paginationTheme = 'bootstrap';

    public $rubric;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $perPageOptions = [12, 24, 36, 48, 60];
    public $perPage;
    public $blockRedirection = FALSE;

    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);

        $this->perPage = session('favoritesPostsPerPage', 12);
        $this->setMode();
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }



    public function switchFavoriteRubric($rubric_id) {
        $this->rubric = Rubric::find($rubric_id);

        if ($this->rubric->isFavorite()) {
            $this->rubric
                ->users()
                ->detach(auth()->user()->id);
        }
        else {
            $this->rubric
                ->users()
                ->attach(auth()->user()->id);
        }

        $this->emitSelf('render');
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

    public function switchSubscription(){

        $user = auth()->user();
        User::query()
            ->where('id', $user->id)
            ->update(['notificationSubscribed' => !$user->notificationSubscribed]);
    }

    public function render() {
        $this->rendered = TRUE;

        $user = User::find(auth()->user()->id);

        return view('livewire.usage.infos-manager', [
            'user' => $user,
            'favoritesPosts' => $user
                ->myFavoritesPosts()
                ->paginate($this->perPage),
            'favoritesRubrics' => $user->rubrics,
        ]);
    }
}
