<?php

namespace App\Http\Livewire\Usage;

use App\CustomFacades\AP;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
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
        'post.published_at' => 'date|nullable',
        'post.expired_at' => 'date|nullable',
    ];

    public function mount($viewBag) {
        $this->backRoute = $viewBag->backRoute;
        $this->currentRubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->mode = $viewBag->mode;
        $this->post = Post::findOrNew($viewBag->post_id);
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

    public function save() {
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
        else{
            // modification
            $this->post->corrector_id = auth()->user()->id;
            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Modification de la mise en forme effectuée avec succès."
                ]);
        }

        // sauvegarde et redirection
        $this->post->save();

        redirect(Rubric::find($this->post->rubric_id)->route()."/{$this->post->id}/edit");
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
                })
        ]);
    }
}
