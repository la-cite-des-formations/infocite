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

/**
 * Composant Livewire pour la consultation et la gestion d'un article spécifique (côté usage).
 * Gère l'affichage, les interactions (vues, favoris), les commentaires et les notifications associées.
 */
class PostManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;
    use WithPinnedHandling;

    /**
     * Rubrique de l'article.
     *
     * @var \App\Models\Rubric
     */
    public $rubric;

    /**
     * Article affiché.
     *
     * @var \App\Models\Post
     */
    public $post;

    /**
     * Contenu du nouveau commentaire.
     *
     * @var string
     */
    public $newComment = '';

    /**
     * Indique si le composant a été rendu.
     *
     * @var bool
     */
    public $rendered = FALSE;

    /**
     * Indique si c'est le premier chargement.
     *
     * @var bool
     */
    public $firstLoad = TRUE;

    protected $listeners = ['modalClosed', 'render', 'deletePost', 'deleteComment'];


    /**
     * Initialisation du composant.
     * Enregistre l'interaction de vue et met à jour l'état de lecture de l'article.
     *
     * @param object $viewBag Sac de données contenant l'ID de l'article.
     */
    public function mount($viewBag) {
        session(['backRoute' => request()->getRequestUri()]);
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->setMode();
        $this->post = Post::find($viewBag->post_id);
        $this->post->ensureInteraction('view');
        $this->post->readers()->syncWithoutDetaching([
            auth()->id() => [
                'is_read' => TRUE
            ]
        ]);
        $this->rubric = $this->post->rubric;
        $this->isFavoriteRubric = $this->rubric->isFavorite;
        $this->isFavoritePost = $this->post->isFavorite;
        $this->setNotifications();
    }

    /**
     * Fonction appelée après le rendu du composant.
     */
    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    /**
     * Ajoute un commentaire à l'article.
     */
    public function commentPost() {
        $commentStr = trim($this->newComment);
        $comment = $commentStr ? new Comment([
            'content' => $commentStr,
            'user_id' => auth()->user()->id,
        ]) : NULL;

        if ($comment) {
            $this->post->comments()->save($comment);
            $this->post->ensureInteraction('comment', now());

            // notification associée
            $newNotification = PostNotification::updateOrCreate(
                ['content_type' => 'CP', 'object_type' => Post::class, 'object_id' => $this->post->id],
                ['release_at' => today()->format('Y-m-d')]
            );
            $newNotification->users()->syncWithoutDetaching($this->post->notificableReaders()->pluck('id'));

            // Recupération de tous les utilisateurs notifiable via Firebase,
            // sauf l'utilisateur courant à l'origine du commentaire
            $users = User::query()
                ->whereHas('employee', function ($employee) {
                    $employee->where('desktop_notifications_granted', TRUE);
                })
                ->where('id', '!=', auth()->user()->id)
                ->where(function ($query) {
                    $query
                        ->whereHas('employee', function ($employee) {
                            $employee->where('notify_only_favorites', FALSE);
                        })
                        ->orWhereHas('favoriteRubrics', function ($favoritesRubrics) {
                            $favoritesRubrics->where('favoriteable_id', $this->post->rubric_id);
                        })
                        ->orWhereHas('favoritePosts',function ($favoritesPosts) {
                            $favoritesPosts->where('favoriteable_id', $this->post->id);
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

    /**
     * Supprime un commentaire.
     *
     * @param int $commentId Identifiant du commentaire.
     */
    public function deleteComment($commentId) {
        $this->post
            ->comments()
            ->where('id', $commentId)
            ->delete();

        $this->emitSelf('render');
    }

    /**
     * Supprime l'article en cours.
     */
    public function deletePost() {
        $this->post->delete();

        redirect($this->post->rubric->route());
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        $this->rendered = TRUE;
        return view('livewire.usage.post-manager');
    }
}
