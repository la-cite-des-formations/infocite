<?php

namespace App\Http\Livewire\Usage;

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

    protected $paginationTheme = 'bootstrap';

    public $rubric;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $perPageOptions = [4, 8, 16];
    public $perPage = 8;
    public $blockRedirection = FALSE;

    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->setMode();
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    public function switchFavoritePost($post_id) {
        $post = Post::find($post_id);

        if ($post->isFavorite()) {
            if ($post->isRead() || $post->tags()) {
                $isFavorite = FALSE;
            }
        }
        else {
            $isFavorite = TRUE;
        }
        if (isset($isFavorite)) {
            $post->readers()->syncWithoutDetaching([
                auth()->user()->id => [
                    'is_favorite' => $isFavorite
                ]
            ]);
        }
        else {
            $post->readers()->detach(auth()->user()->id);
        }
        $this->emitSelf('render');
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

    public function render() {
        $this->rendered = TRUE;

        $user = User::find(auth()->user()->id);

        return view('livewire.usage.infos-manager', [
            'user' => $user,
            'favoritesPosts' => $user->myFavoritesPosts()
                ->paginate($this->perPage),
            'favoritesRubrics' => $user->rubrics()
                ->paginate($this->perPage),
        ]);
    }
}
