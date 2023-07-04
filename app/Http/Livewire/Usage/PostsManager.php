<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use App\Rubric;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class PostsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 12, 16];
    public $perPage = 8;

    public $rubric;
    public $firstLoad = TRUE;

    protected $listeners = ['render'];

    public function mount($viewBag) {
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
    }

    public function switchFavoritePost($post_id) {
        if ($this->firstLoad) {
            $this->firstLoad = FALSE;
        }

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

    public function switchFavoriteRubric() {
        if ($this->firstLoad) $this->firstLoad = FALSE;

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

    public function render() {
        return view('livewire.usage.posts-manager', [
            'posts' => Post::query()
                ->whereIn('id', Post::query()
                    ->when($this->rubric->segment != 'une', function ($query) {
                        $query
                            ->where('rubric_id', $this->rubric->id)
                            ->orderByRaw('published_at DESC, created_at DESC');
                    })
                    ->when($this->rubric->segment == 'une', function ($query) {
                        $query
                            ->whereIn('rubric_id', User::find(auth()->user()->id)->myRubrics()->pluck('id'))
                            ->orderByRaw('published_at DESC, created_at DESC');
                    })
                    ->get()
                    ->filter(function ($post) {
                        return User::find(auth()->user()->id)->can('view', $post);
                    })
                    ->pluck('id')
                )
                ->paginate($this->perPage),
        ]);
    }
}
