<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithPinnedHandling;
use App\Post;
use App\Rubric;
use App\User;
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
    use WithFilter;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 12, 16, 24, 48, 60];
    public $perPage = 16;

    public $rubric;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;

    protected $listeners = ['modalClosed', 'deletePost'];

    public $filter =[
        'favoritePosts'=>'',
        'notViewPosts'=>'',
        'mostConsultedPosts'=>'',
        'mostRecentlyPosts'=>'',
        'mostCommentedPosts'=>'',
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

    public function pinnedPosts()
    {
        return Post::query()
            ->where('is_pinned', '=', TRUE)
            ->get();
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


    public function render()
    {

        return view('livewire.usage.posts-manager', [

            'posts' => $this->updatedFilter(),
            'pinnedPost' => $this->pinnedPosts(),
        ]);
    }

//***************************************************************************************//
//********************************Filter/Sort********************************************//
//***************************************************************************************//

    public function favoritePosts()
    {
        $user = User::find(auth()->user()->id);
        return
            $user
                ->myFavoritesPosts()
                ->paginate($this->perPage);
    }

    public function notViewPosts()
    {
        $userId = auth()->user()->id;
        return Post::whereDoesntHave('readers', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->orWhereHas('readers', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('is_read', false);
            })
            ->paginate($this->perPage);

    }

    public function mostConsultedPosts()
    {
        return Post::withCount('readers')
        ->orderByDesc('readers_count')
        ->paginate($this->perPage);
    }

    public function mostCommentedPosts()
    {
        return Post::withCount('comments')
            ->orderByDesc('comments_count')
            ->paginate($this->perPage);
    }

    public function mostRecentlyPosts(){

        return Post::query()
            ->orderBy('updated_at','DESC')
            ->paginate($this->perPage);
    }
    public function updatedFilter()
    {
        foreach ($this->filter as $key => $value) {
            if ($value == 'on') {
                switch ($key) {
                    case 'favoritePosts' :
                        return $this->favoritePosts();

                    case 'notViewPosts' :
                        return $this->notViewPosts();

                    case 'mostConsultedPosts' :
                        return $this->mostConsultedPosts();

                    case 'mostRecentlyPosts' :
                        return $this->mostRecentlyPosts();

                    case 'mostCommentedPosts' :
                        return $this->mostCommentedPosts();


                }
            }
        }

        return $this->allPosts();
    }
    public function updatingFilter(){
        foreach ($this->filter as $key => $value){
            $this->filter[$key] = '';
        }
    }


}
