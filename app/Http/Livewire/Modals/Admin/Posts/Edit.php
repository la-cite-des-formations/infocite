<?php

namespace App\Http\Livewire\Modals\Admin\Posts;

use App\CustomFacades\AP;
use App\Post;
use App\Rubric;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;
    use WithIconpicker;

    public $post;
    public $mode;
    public $canAdd = TRUE;
    public $formTabs;

    protected $listeners = ['render', 'contentChange'];
    protected $rules = [
        'post.title' => 'required|string|max:255',
        'post.icon' => 'required|string|max:255',
        'post.content' => 'required|string',
        'post.rubric_id' => 'required',
        'post.published' => 'required|boolean',
    ];

    public function contentChange($content) {
        $this->post->content = $content;
    }

    public function setPost($id = NULL) {
        if (is_null($id)) {
            $this->emit('deleteContent');
        }

        $this->post = $this->post ?? Post::findOrNew($id);

        if ($this->mode === 'creation') $this->post->published = FALSE;

        $this->initTinymce();

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.posts',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir l'article'",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setPost($id ?? NULL);
    }

    public function initTinymce(){
        $this->dispatchBrowserEvent('initTinymce');
    }

    public function refresh() {
        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Réinitialisation effectuée avec succès."
            ])
            ->self();
    }

    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->post = NULL;
        if ($mode !== 'view') {
            $this->setPost();
        }
        else {
            $this->post = Post::find($this->post->id);
        }
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        if ($this->mode === 'creation') {
            $this->post->author_id = Auth::user()->id;
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création de l'article effectuée avec succès."
                ])
                ->self();
        }
        else {
            $this->post->corrector_id = Auth::user()->id;
            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification de l'article effectuée avec succès."
                ])
                ->self();
        }

        $this->post
            ->save();
    }

    public function render($messageBag = NULL){
        $searchIcons = $this->searchIcons;
        if ($messageBag) {
            extract($messageBag);
            session()->flash('alertClass', $alertClass);
            session()->flash('message', $message);
        }

        return $this->mode === 'view' ?
            view('livewire.modals.admin.posts.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un article',
                'rubrics' => Rubric::query()
                    ->where('contains_posts', TRUE)
                    ->where('rank', '!=', '0')
                    ->orderByRaw('position ASC, rank ASC')
                    ->get(),
                'modalSize' => 'modal-xl',
                'haveTiny' => TRUE,
                'icons' => AP::getMaterialIconsCodes()
                    ->when($searchIcons, function ($icons) use ($searchIcons) {
                    return $icons->filter(function ($miCode, $miName) use ($searchIcons) {
                        return str_contains($miName, $searchIcons);
                    });
                }),
            ]);
    }
}
