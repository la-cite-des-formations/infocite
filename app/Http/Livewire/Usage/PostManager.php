<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithPinnedHandling;
use App\Models\Notification as PostNotification;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Livewire\Component;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppNotification;

class PostManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;
    use WithPinnedHandling;

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
        $commentStr = trim($this->newComment);
        $comment = $commentStr ? new Comment([
            'content' => $commentStr,
            'user_id' => auth()->user()->id,
        ]) : NULL;

        if ($comment) {
            $this->post->comments()->save($comment);

            // notification associée
            $newNotification = PostNotification::updateOrCreate(
                ['content_type' => 'CP', 'object_type' => Post::class, 'object_id' => $this->post->id],
                ['release_at' => today()->format('Y-m-d')]
            );
            $newNotification->users()->syncWithoutDetaching($this->post->notificableReaders()->pluck('id'));

            // Recupération de tous les utilisateurs ayant la rubrique de l'article ou bien l'article lui même en favoris,
            // sauf l'utilisateur courant à l'origine de l'action (création ou modification de l'article)
            $users = User::query()
                ->where('id', '!=', auth()->user()->id)
                ->where('desktop_notifications_granted',true)
                ->where(function ($query) {
                    $query
                        ->where('notify_only_favorites', FALSE)
                        ->orWhereHas('myFavoritesRubrics', function ($favoritesRubrics) {
                            $favoritesRubrics->where('rubric_id', $this->post->rubric_id);
                        })
                        ->orWhereHas('myFavoritesPosts',function ($favoritesPosts) {
                            $favoritesPosts->where('post_id', $this->post->id);
                        });
                })
                ->get();

            // Envoi de la notification aux utilisateurs concernés
            Notification::send($users, new AppNotification([
                'type' => $newNotification->content_type,
                'post' => $this->post
            ]));

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
