<?php

namespace App\Http\Livewire\Usage;

use App\Comment;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithFavoritesHandling;
use App\Notification;
use App\Post;
use Livewire\Component;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;

class PostManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;

    public $rubric;
    public $post;
    public $newComment = '';
    public $rendered = FALSE;
    public $firstLoad = TRUE;

    protected $listeners = ['modalClosed', 'render', 'deletePost', 'deleteComment'];

    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->setMode();
        $this->post = Post::find($viewBag->post_id);
        $this->post->readers()->syncWithoutDetaching([
            auth()->user()->id => [
                'is_read' => TRUE
            ]
        ]);
        $this->rubric = $this->post->rubric;
        $this->isFavoriteRubric = $this->rubric->isFavorite();
        $this->isFavoritePost = $this->post->isFavorite();
        $this->setNotifications();
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    public function commentPost() {
        $newComment = trim($this->newComment);
        $comment = $newComment ? new Comment([
            'content' => $newComment,
            'user_id' => auth()->user()->id,
        ]) : NULL;

        if ($comment) {
            $this->post->comments()->save($comment);

            // notification associée
            $newNotification = Notification::updateOrCreate(
                ['content_type' => 'CP', 'post_id' => $this->post->id],
                ['release_at' => today()->format('Y-m-d')]
            );
            $newNotification->users()->syncWithoutDetaching($this->post->notificableReaders()->pluck('id'));

            $this->emitSelf('render');
        }

        $this->newComment = '';
    }

    public function deleteComment($commentId) {
        $this->post
            ->comments()
            ->where('id', $commentId)
            ->delete();

        $this->emitSelf('render');
    }

    public function deletePost() {
        $this->post->delete();

        redirect($this->post->rubric->route());
    }

    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.post-manager');
    }
}
