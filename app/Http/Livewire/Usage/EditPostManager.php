<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;
use App\Http\Livewire\HandleTinymceContent;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithPinnedHandling;
use App\Models\Notification as PostNotification;
use App\Models\Post;
use App\Models\Group;
use App\Models\Right;
use App\Models\Roles;
use App\Models\Rubric;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppNotification;

/**
 * Composant Livewire pour l'édition d'un article existant ou la création d'un nouveau.
 */
class EditPostManager extends Component
{
    use HandleTinymceContent;
    use WithModal;
    use WithAlert;
    use WithIconpicker;
    use WithPinnedHandling;

    /**
     * Route de retour après enregistrement.
     *
     * @var string
     */
    public $backRoute;

    /**
     * Rubrique parente de l'article.
     *
     * @var \App\Models\Rubric
     */
    public $currentRubric;

    /**
     * Mode d'édition ('creation' ou 'edition').
     *
     * @var string
     */
    public $mode;

    /**
     * Instance de l'article en cours d'édition.
     *
     * @var \App\Models\Post
     */
    public $post;

    /**
     * Indique si les commentaires sont bloqués pour cet article.
     *
     * @var bool
     */
    public $blockComments;

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['modalClosed', 'save', 'contentChange', 'contentPaste', 'applyTemplateConfirmed', 'extractTemplateConfirmed'];

    /**
     * Règles de validation pour l'article.
     *
     * @var array
     */
    protected function rules() {
        return [
            'post.title'                      => 'required|string|max:255',
            'post.icon'                       => 'required|string|max:255',
            'post.content'                    => 'required|string',
            'post.rubric_id'                  => $this->post->is_template ? 'nullable' : 'required',
            'post.published'                  => '',
            'post.is_pinned'                  => '',
            'post.auto_delete'                => '',
            'post.published_at'               => 'date|nullable',
            'post.expired_at'                 => 'date|nullable',
            'post.is_acknowledgment_required' => 'boolean',
            'post.is_rating_enabled'          => 'boolean',
            'post.is_template'                => 'boolean',
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant la rubrique, le mode et l'ID de l'article.
     */
    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->backRoute = session('backRoute');
        $this->currentRubric = $viewBag->rubric;
        $this->mode = $viewBag->mode;
        $this->post = Post::findOrNew($viewBag->post_id);
        if ($this->mode === 'creation' && $this->currentRubric->name != 'Une' && !$this->post->rubric_id) {
            $this->post->rubric_id = $this->currentRubric->id;
        }
        // Détection du mode modèle via paramètre URL
        if (request()->query('template') && $this->mode === 'creation') {
            $this->post->is_template = true;
        }

        // Pré-remplissage à partir d'un modèle (Création d'un article basé sur un modèle)
        if ($this->mode === 'creation' && $templateId = request()->query('from_template')) {
            $sourceTemplate = Post::templates()->find($templateId);
            if ($sourceTemplate) {
                $this->post->title = $sourceTemplate->title;
                $this->post->icon = $sourceTemplate->icon;
                $this->post->content = $sourceTemplate->content;
            }
        }

        $this->blockComments = !$this->post->isCommentable() && $this->mode == 'edition';
        $this->initTinymceContent('post.content');
    }

    /**
     * Gère les dates de publication lors de l'activation/désactivation de la publication.
     */
    public function updatedPostPublished() {
        if ($this->post->published) {
            $this->post->published_at = today()->format('Y-m-d');
            $this->post->is_pinned = $this->post->getOriginal('is_pinned');
        }
        else {
            $this->post->published_at = NULL;
            $this->post->is_pinned = FALSE;
            // if ($this->post->is_pinned) {
            //     $this->switchPinnedPost($this->post->id);
            // }
        }
        $this->post->expired_at = NULL;
    }

    /**
     * Enregistre l'article (création ou modification).
     * Gère les notifications (Firebase et internes) et la mise au propre du contenu TinyMCE.
     *
     * @param string $redirectionRoute Route de redirection après succès.
     */
    public function save($redirectionRoute = 'post.edit') {
        $this->post->published_at = $this->post->published_at ?: NULL;
        $this->post->expired_at = $this->post->expired_at ?: NULL;
        $this->post->is_pinned = $this->post->is_pinned ?? FALSE;
        $this->post->is_acknowledgment_required = $this->post->is_acknowledgment_required ?? FALSE;
        $this->post->is_rating_enabled = $this->post->is_rating_enabled ?? FALSE;
        $this->post->is_template = $this->post->is_template ?? FALSE;
        $this->post->published = $this->post->published ?? FALSE;

        $this->validate();

        if ($this->mode === 'creation') {
            // création
            $this->post->author_id = auth()->id();
            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Création de la mise en forme effectuée avec succès."
                ]);
        }
        else {
            // modification
            $this->post->corrector_id = auth()->id();

            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Modification de la mise en forme effectuée avec succès."
                ]);
        }

        // sauvegarde
        $this->post->save();

        // Pour les modèles : pas de galerie, droits, interactions ni notifications
        if ($this->post->is_template) {
            redirect()->route('post.edit', [
                'rubric' => optional($this->post->rubric)->segmentPath() ?? 'une',
                'post_id' => $this->post->id,
            ]);
            return;
        }
        
        // Traitement de la galerie photos (bascule du cache vers BDD)
        \App\Http\Livewire\Usage\PostGallery::processTempGallery($this->post->id, session('post_gallery_token'));

        // Gestion des droits (Après la sauvegarde pour avoir l'ID en mode création)
        $globalGroup = Group::query()
            ->where('type', 'S')
            ->where('name', 'GLOBAL')
            ->first();

        $commentRight = Right::query()
            ->where('name', 'comments')
            ->first();

        if ($this->blockComments) {
            $commentRight
                ->groups()
                ->attach([
                    $globalGroup->id => [
                        'resource_type' => 'Post',
                        'resource_id' => $this->post->id,
                        'priority' => 2,
                        'roles' => Roles::NONE
                    ]
                ]);
        }
        else {
            $commentRight
                ->groups()
                ->newPivotQuery()
                ->where('rightable_type', 'Group')
                ->where('rightable_id', $globalGroup->id)
                ->where('resource_type', 'Post')
                ->where('resource_id', $this->post->id)
                ->delete();
        }

        if ($this->post->hasInteraction('create') || $this->post->hasInteraction('update')) {
            $this->post->ensureInteraction('update', $this->post->updated_at);
        }
        else {
            $this->post->ensureInteraction('create', $this->post->created_at);

            // mise en favoris de l'article pour l'auteur au moment de la création
            if (! $this->post->favoritedBy()->where('user_id', auth()->id())->exists()) {
                $this->post->favoritedBy()->attach(auth()->id());
            }
        }

        // enregistrement en bdd de la notification associée si l'article est paru
        if ($this->post->released) {
            $newPostNotification = PostNotification::query()
                ->where('content_type', 'NP')
                ->where('object_type', Post::class)
                ->where('object_id', $this->post->id);

            if ($newPostNotification->exists()) {
                $newPostNotification->update(['release_at' => $this->post->published_at]);

                $postNotification = PostNotification::updateOrCreate(
                    ['content_type' => 'UP', 'object_type' => Post::class, 'object_id' => $this->post->id],
                    ['release_at' => $this->post->updated_at]
                );
            }
            else {
                $postNotification = PostNotification::create(
                    ['content_type' => 'NP', 'object_type' => Post::class, 'object_id' => $this->post->id, 'release_at' => $this->post->published_at]
                );
            }

            $postNotification
                ->users()
                ->syncWithoutDetaching($this->post->notificableReaders()->pluck('id'));

            // Recupération de tous les utilisateurs notifiables via Firebase,
            // sauf l'utilisateur courant à l'origine de l'action (création ou modification de l'article)
            $users = User::query()
                ->where('id', '!=', auth()->user()->id)
                ->whereHas('employee', function ($employee) {
                    $employee->where('desktop_notifications_granted', TRUE);
                })
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

            // Envoi de la notification firebase aux utilisateurs concernés
            Notification::send($users, new AppNotification([
                'type' => $postNotification->content_type,
                'post' => $this->post,
            ]));
        }

        // redirection
        redirect()->route($redirectionRoute, [
            'rubric' => Rubric::find($this->post->rubric_id)->segmentPath(),
            'post_id' => $this->post->id,
        ]);
    }

    /**
     * Enregistre l'article actuel en tant que modèle.
     */
    /**
     * Ouvre la modale pour l'extraction d'un modèle.
     */
    public function openExtractTemplateModal($currentContent = null) {
        if ($currentContent !== null) {
            $this->post->content = $currentContent;
        }

        $this->showModal('extract-template', [
            'currentContent' => $this->post->content,
            'isDirty' => $this->post->isDirty() || !$this->post->exists,
        ]);
    }

    /**
     * Extrait le contenu actuel comme modèle, avec gestion optionnelle de la redirection
     * et de la sauvegarde de l'article source.
     */
    public function extractTemplateConfirmed($redirect, $saveSourcePost) {
        $templateRubricId = $this->post->rubric_id ?: NULL;

        if ($saveSourcePost && !$this->post->is_template) {
            if (empty($this->post->rubric_id) && $this->post->exists) {
                $this->post->rubric_id = $this->post->getOriginal('rubric_id');
            }
            $this->validate(); // Validation stricte de l'article source
            $this->post->save();
        } else {
            $this->validate([
                'post.title' => 'required|string|max:255',
                'post.icon' => 'required|string|max:255',
                'post.content' => 'required|string',
            ]);
        }

        $template = new Post();
        $template->title = $this->post->title;
        $template->content = $this->post->content;
        $template->icon = $this->post->icon;
        $template->rubric_id = $templateRubricId;
        $template->is_template = true;
        $template->author_id = auth()->id();
        $template->corrector_id = null;
        $template->published = false;
        $template->published_at = null;
        $template->expired_at = null;
        $template->is_pinned = false;
        $template->auto_delete = false;
        $template->is_acknowledgment_required = false;
        $template->is_rating_enabled = false;
        $template->save();

        if ($redirect) {
            return redirect()->route('post.edit', ['rubric' => optional($template->rubric)->segmentPath() ?? 'une', 'post_id' => $template->id]);
        }

        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Une copie de cet article a été extraite comme modèle avec succès."
        ]);
    }

    /**
     * Ouvre la modale pour l'application d'un modèle.
     */
    public function openApplyTemplateModal($currentContent = null) {
        if ($currentContent !== null) {
            $this->post->content = $currentContent;
        }

        // Enlève l'erreur d'échappement pour de longs contenus HTML
        $this->showModal('apply-template', [
            'currentContent' => $this->post->content,
            'rubricId' => $this->currentRubric->id ?? null
        ]);
    }

    /**
     * Applique un modèle au contenu actuel (ajout par concaténation) via la modale.
     *
     * @param int $templateId ID du modèle à appliquer.
     */
    public function applyTemplateConfirmed($templateId) {
        if (!$templateId) return;

        $template = Post::templates()->findOrFail($templateId);
        $this->post->content .= $template->content;

        // On émet vers JS pour injecter dans TinyMCE
        $this->emit('insertCleanContent', $template->content);

        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Le modèle a été appliqué au contenu."
        ]);
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.usage.edit-post-manager', [
            'rubrics' => Rubric::query()
                ->where('contains_posts', TRUE)
                ->where('rank', '!=', '0')
                ->orderByRaw('position ASC, rank ASC')
                ->get(),
            'icons' => $this->getMiCodes(),
            'templates' => Post::templates()
                ->where(function($query) {
                    $query->whereNull('rubric_id')
                          ->orWhere('rubric_id', $this->currentRubric->id);
                })
                ->get(),
        ]);
    }
}
