<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use App\Rubric;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithModal;
use App\Notification;

class PostsManager extends Component
{
    use WithPagination;
    use WithModal;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 12, 16];
    public $perPage = 8;

    public $rubric;
    public $notifications;
    public $firstLoad = TRUE;

    protected $closedModalCallback = ['updateNotifications', 'setNotifications'];
    protected $listeners = ['modalClosed', 'render'];

    public function setNotifications() {
        $this->notifications = auth()->user()
            ->myNotifications()
            ->where('consulted', FALSE)
            ->sortByDesc('created_at');
    }

    public function mount($viewBag) {
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->setNotifications();
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

    public function updateNotifications() {
        $notificationsIds = $this->notifications->pluck('id');

        Notification::query()
            ->whereIn('id', $notificationsIds)
            ->update(['consulted' => TRUE]);
    }

    public function updatedPerPage() {
        $this->resetPage();
    }

    public function render() {
        $user = auth()->user();

        return view('livewire.usage.posts-manager', [
            'posts' => Post::query()
                ->whereIn('id', Post::query()
                    ->when($this->rubric->segment != 'une', function ($query) {
                        $query
                            ->where('rubric_id', $this->rubric->id)
                            ->orderByRaw('published_at DESC, created_at DESC');
                    })
                    ->when($this->rubric->segment == 'une', function ($query) use ($user) {
                        $query
                            ->whereIn('rubric_id', $user->myRubrics()->pluck('id'))
                            ->orderByRaw('published_at DESC, created_at DESC');
                    })
                    ->get()
                    ->filter(function ($post) use ($user) {
                        return $user->can('view', $post);
                    })
                    ->pluck('id')
                )
                ->orderByRaw('created_at DESC')
                ->paginate($this->perPage),
        ]);
    }
}
