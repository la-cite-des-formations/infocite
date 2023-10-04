<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use App\Post;
use App\Rubric;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;

//use App\Notification;

class PostsManager extends Component
{
    use WithPagination;
    use WithModal;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 12, 16, 24, 48, 60];
    public $perPage = 16;

    public $rubric;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;

    protected $listeners = ['modalClosed', 'deletePost'];

    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->setMode();
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->isFavoriteRubric = $this->rubric->isFavorite();
        $this->setNotifications();
    }

    public function updatedPerPage() {
        $this->resetPage();
    }

    public function deletePost($postId) {
        Post::find($postId)->delete();
    }

    public function redirectToPost($postId) {
        if (!$this->blockRedirection) {
            redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
        }
        $this->blockRedirection = FALSE;
    }

    public function blockRedirection() {
        $this->firstLoad = FALSE;
        $this->blockRedirection = TRUE;
    }

    public function render() {
        $user = auth()->user();

        return view('livewire.usage.posts-manager', [
            'posts' => Post::query()
                ->whereIn('id', Post::query()
                    ->when($this->rubric->name != 'Une' && $this->rubric->name != 'Archives', function ($query) {
                        $query
                            ->where('rubric_id', $this->rubric->id);
                    })
                    ->when($this->rubric->name == 'Une', function ($query) use ($user) {
                        $query
                            ->whereIn('rubric_id', $user->myRubrics()->pluck('id'));
                    })
                    ->when($this->rubric->name == 'Archives', function ($query) use ($user) {
                        $query
                            ->whereIn('rubric_id', $user->myRubrics()->pluck('id'))
                            ->where('published', TRUE)
                            ->where('expired_at', '<=', today()->format('Y-m-d'))
                            ->where('auto_delete', FALSE);
                    })
                    ->get()
                    ->filter(function ($post) use ($user) {
                        return
                            $user->can('view', $post) && (
                                $this->mode == 'edition' ||
                                $post->released && $this->rubric->name != 'Archives' ||
                                $post->archived && $this->rubric->name == 'Archives'
                            );
                    })
                    ->pluck('id')
                )
                ->when($this->mode == 'edition', function ($query) {
                    $query->orderBy('published');
                })
                ->orderByRaw('published_at DESC, updated_at DESC, created_at DESC')
                ->paginate($this->perPage),
        ]);
    }
}
