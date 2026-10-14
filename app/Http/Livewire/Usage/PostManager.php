<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithPinnedHandling;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Livewire\Component;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;
use App\Services\PostNotificationService;

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

    protected $listeners = ['modalClosed', 'render', 'deletePost', 'deleteComment', 'refreshNotifications' => 'refreshNotifications'];


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

        $this->autoOpenGuideline('read-post');
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

            app(PostNotificationService::class)->notifyComment($this->post);

            $this->emitSelf('render');
        }

        $this->newComment = '';
    }

    /**
     * Enregistre l'acquittement de lecture de l'article par l'utilisateur courant.
     * Stocké dans la table interactions avec le type 'acknowledge'.
     */
    public function acknowledgeRead() {
        if (! $this->post->is_acknowledgment_required) return;
        if ($this->post->isAcknowledged()) return;

        $this->post->ensureInteraction('acknowledge', now());
        $this->emitSelf('render');
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
