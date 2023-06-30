<?php

namespace App\Http\Livewire\Usage;

use App\Comment;
use App\Http\Livewire\WithAlert;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Http\Livewire\WithModal;


class PostManager extends Component
{
    use WithModal;
    use WithAlert;
    public $post;
    public $newComment = '';
    public $firstLoad = TRUE;

    protected $listeners = ['modalClosed', 'render', 'deletePost', 'deleteComment'];

    public function mount($viewBag) {
        $this->post = Post::find($viewBag->post_id);
        $this->post->readers()->syncWithoutDetaching([
            Auth::user()->id => [
                'is_read' => TRUE
            ]
        ]);
    }

    public function hydrate() {
        if ($this->firstLoad) $this->firstLoad = FALSE;
    }

    public function switchFavorite() {
        if ($this->post->isFavorite()) {
            if ($this->post->isRead() || $this->post->tags()) {
                $isFavorite = FALSE;
            }
        }
        else {
            $isFavorite = TRUE;
        }
        if (isset($isFavorite)) {
            $this->post->readers()->syncWithoutDetaching([
                Auth::user()->id => [
                    'is_favorite' => $isFavorite
                ]
            ]);
        }
        else {
            $this->post->readers()->detach(Auth::user()->id);
        }
        $this->emitSelf('render');
    }

    public function commentPost() {
        $newComment = trim($this->newComment);
        $comment = $newComment ? new Comment([
            'content' => $newComment,
            'user_id' => Auth::user()->id,
        ]) : NULL;

        if ($comment) {
            $this->post->comments()->save($comment);
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
        $redirection = $this->post->rubric->route();
        $this->post->delete();
        redirect($redirection);
    }

    public function render() {
        return view('livewire.usage.post-manager');
    }
}
