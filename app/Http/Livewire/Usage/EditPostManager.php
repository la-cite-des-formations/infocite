<?php

namespace App\Http\Livewire\Usage;

use App\CustomFacades\AP;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
use App\Notification;
use App\Post;
use App\Right;
use App\Roles;
use App\Rubric;
use Livewire\Component;

class EditPostManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithIconpicker;

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
        'post.auto_delete' => '',
        'post.published_at' => 'date|nullable',
        'post.expired_at' => 'date|nullable',
    ];

    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->backRoute = session('backRoute');
        $this->currentRubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
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
        }

        // redirection
        redirect()->route($redirectionRoute, [
            'rubric' => Rubric::find($this->post->rubric_id)->route(),
            'post_id' => $this->post->id,
        ]);
    }

    public function render() {
        $searchIcons = $this->searchIcons;
        return view('livewire.usage.edit-post-manager', [
            'rubrics' => Rubric::query()
                ->where('contains_posts', TRUE)
                ->where('rank', '!=', '0')
                ->orderByRaw('position ASC, rank ASC')
                ->get(),
            'icons' => AP::getMaterialIconsCodes()
                ->when($searchIcons, function ($icons) use ($searchIcons) {
                    return $icons->filter(function ($miCode, $miName) use ($searchIcons) {
                        return str_contains($miName, $searchIcons);
                    });
                }),
        ]);
    }
}
