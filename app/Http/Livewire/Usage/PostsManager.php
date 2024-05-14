<?php

namespace App\Http\Livewire\Usage;


use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithFilterPosts;
use App\Http\Livewire\WithPinnedHandling;
use App\Post;
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
    use WithPinnedHandling;
    use WithFilterPosts;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 12, 16, 24, 48, 60];
    public $perPage = 16;
    public $displayPosts='grid';
    public $listeDisplay = false;

    public $rubric;
    protected $posts;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;


    protected $listeners = ['modalClosed', 'deletePost'];

    public $filter = [
        'favoritePosts' => '',
        'notViewPosts' => '',
        'postsInFavoritesRubrics' => '',
        'allPosts' => '',
    ];
    public $sorter = [
        'mostConsultedPosts' => '',
        'mostRecentlyPosts' => '',
        'mostCommentedPosts' => '',
    ];

    public function mount($viewBag)
    {
        session([
            'backRoute' => request()->getRequestUri(),
            'appsBackRoute' => request()->getRequestUri(),
        ]);

        $this->perPage = session('postsPerPage', 16);
        $this->setMode();
        $this->rubric = $viewBag->rubric;
        $this->isFavoriteRubric = $this->rubric->isFavorite();
        $this->setNotifications();
        $this->lastFilterActive();
        $this->lastSorterActive();
    }

    public function booted()
    {
        $this->firstLoad = !$this->rendered;
    }

    public function updatedPerPage()
    {
        session(['postsPerPage' => $this->perPage]);
        $this->resetPage();
    }

    public function deletePost($postId)
    {
        Post::find($postId)->delete();
    }

    public function redirectToPost($postId)
    {
        if (!$this->blockRedirection) {
            redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
        }
        $this->blockRedirection = FALSE;
    }

    public function blockRedirection()
    {
        $this->blockRedirection = TRUE;
    }

    public function displayListPosts()
    {
        $this->displayPosts = 'list';
        $this->resetPage();
    }
    public function displayGridPosts()
    {
        $this->displayPosts = 'grid';
        $this->resetPage();
    }

    public function allPosts()
    {
        $user = auth()->user();
        return Post::query()
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
                        $user->can('read', $post) && (
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
            ->paginate($this->perPage);
    }

    protected function getFilteredOrSortedPosts(){

        if (session()->has('lastFilter') && $this->rubric->name === 'Une'){
            return $this->lastFilterActive();
        }elseif (session()->has('lastSorter') && $this->rubric->name === 'Une'){
            return $this->lastSorterActive();
        }else{
            return $this->allPosts();
        }
    }

    public function render()
    {
        $this->rendered = TRUE;
        return view('livewire.usage.posts-manager', [
            'posts' =>$this->getFilteredOrSortedPosts(),
            'pinnedPost' => $this->pinnedPosts(),
        ]);
    }


}
