<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;
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

class EditPostManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithIconpicker;
    use WithPinnedHandling;

    public $backRoute;
    public $currentRubric;
    public $mode;
    public $post;
    public $blockComments;

    protected $listeners = ['modalClosed', 'save', 'contentChange'];
    protected $rules = [
        'post.title' => 'required|string|max:255',
        'post.icon' => 'required|string|max:255',
        'post.content' => 'required|string',
        'post.rubric_id' => 'required',
        'post.published' => '',
        'post.is_pinned' => '',
        'post.auto_delete' => '',
        'post.published_at' => 'date|nullable',
        'post.expired_at' => 'date|nullable',

    ];

    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->backRoute = session('backRoute');
        $this->currentRubric = $viewBag->rubric;
        $this->mode = $viewBag->mode;
        $this->post = Post::findOrNew($viewBag->post_id);
        if ($this->currentRubric->name != 'Une' && !$this->post->rubric_id) {
            $this->post->rubric_id = $this->currentRubric->id;
        }
        $this->blockComments = !$this->post->isCommentable() && $this->mode == 'edition';
    }

    public function contentChange($content) {
        $this->post->content = $content;
    }

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

    public function save($redirectionRoute = 'post.edit') {
        $this->post->published_at = $this->post->published_at ?: NULL;
        $this->post->expired_at = $this->post->expired_at ?: NULL;
        $this->post->is_pinned = $this->post->is_pinned ?? FALSE;

        $this->validate();

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

        if ($this->mode === 'creation') {
            // création
            $this->post->author_id = auth()->user()->id;
            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Création de la mise en forme effectuée avec succès."
                ]);
        }
        else {
            // modification
            $this->post->corrector_id = auth()->user()->id;

            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Modification de la mise en forme effectuée avec succès."
                ]);
        }

        // sauvegarde
        $this->post->save();

        // mise en favoris de l'article pour l'éditeur
        $this->post->readers()->syncWithoutDetaching([
            auth()->user()->id => [
                'is_favorite' => TRUE
            ]
        ]);

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
                        ->orWhereHas('myFavoritesRubrics', function ($favoritesRubrics) {
                            $favoritesRubrics->where('rubric_id', $this->post->rubric_id);
                        })
                        ->orWhereHas('myFavoritesPosts',function ($favoritesPosts) {
                            $favoritesPosts->where('post_id', $this->post->id);
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
            'rubric' => Rubric::find($this->post->rubric_id)->route(),
            'post_id' => $this->post->id,
        ]);
    }

    public function render() {
        return view('livewire.usage.edit-post-manager', [
            'rubrics' => Rubric::query()
                ->where('contains_posts', TRUE)
                ->where('rank', '!=', '0')
                ->orderByRaw('position ASC, rank ASC')
                ->get(),
            'icons' => $this->getMiCodes(),
        ]);
    }
}
