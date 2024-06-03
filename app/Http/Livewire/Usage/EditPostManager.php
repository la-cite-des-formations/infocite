<?php

namespace App\Http\Livewire\Usage;

use App\Events\NotificationPusher;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithPinnedHandling;
use App\Notification;
use App\Post;
use App\Right;
use App\Roles;
use App\Rubric;
use App\User;
use Livewire\Component;

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
    public $pinPost;

    protected $listeners = ['modalClosed', 'save', 'contentChange'];
    protected $rules = [
        'post.title' => 'required|string|max:255',
        'post.icon' => 'required|string|max:255',
        'post.content' => 'required|string',
        'post.rubric_id' => 'required',
        'post.published' => '',
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
        }
        else {
            $this->post->published_at = NULL;
        }
        $this->post->expired_at = NULL;
    }

    public function save($redirectionRoute = 'post.edit') {
        $this->post->published_at = $this->post->published_at ?: NULL;
        $this->post->expired_at = $this->post->expired_at ?: NULL;

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
        if($this->pinPost){
            $this->countPinnedPosts();
            if( $this->countPinnedPosts < 4){
                $this->post->is_pinned = TRUE;

            }else{
                $this->addError('post.pinPost', 'Le nombre maximum d\'articles épinglé (4 articles) est déjà atteint : vous ne pouvez pas épingler cet article');
                return;
            }

        }

        // sauvegarde
        $this->post->save();

        // notification associée
        if ($this->post->published) {
            $newPostNotification = Notification::query()
                ->where('content_type', 'NP')
                ->where('post_id', $this->post->id);

            if ($newPostNotification->exists()) {
                $newPostNotification->update(['release_at' => $this->post->published_at]);

                $postNotification = Notification::updateOrCreate(
                    ['content_type' => 'UP', 'post_id' => $this->post->id],
                    ['release_at' => $this->post->published_at]
                );
            }
            else {
                $postNotification = Notification::create(
                    ['content_type' => 'NP', 'post_id' => $this->post->id, 'release_at' => $this->post->published_at]
                );
            }
            $postNotification
                ->users()
                ->syncWithoutDetaching($this->post->notificableReaders()->pluck('id'));

            //Boradcasting notification
            //Recupérer tout les utilisateurs qui ont la rubrique de l'article en favoris OU l'article lui même en favori
            $currentPostRubricId = Post::query()->where('id',$this->post->id)->pluck('rubric_id')->first();
            $userIds = User::query()
                ->where('notificationSubscribed',true)
                ->whereHas('myFavoritesRubrics',function ($query) use ($currentPostRubricId) {
                $query->where('rubric_id',$currentPostRubricId);
            })->orWhereHas('myFavoritesPosts',function ($query) {
                $query->where('post_id','=',$this->post->id);
            })->get()->pluck('id')->toArray();

            //Broadcaster la notification
            broadcast( new NotificationPusher($postNotification, $userIds))->toOthers();


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
