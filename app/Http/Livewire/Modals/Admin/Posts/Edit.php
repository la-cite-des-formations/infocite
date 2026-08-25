<?php

namespace App\Http\Livewire\Modals\Admin\Posts;

use App\Models\Post;
use App\Models\Rubric;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'édition d'un article.
 */
class Edit extends Component
{
    use WithAlert;
    use WithIconpicker;

    /**
     * Modèle de l'article.
     *
     * @var \App\Models\Post
     */
    public $post;

    /**
     * Mode d'affichage ou d'édition.
     *
     * @var string
     */
    public $mode;

    /**
     * Indique si l'ajout est autorisé.
     *
     * @var bool
     */
    public $canAdd = TRUE;

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    public $attach_to_agenda = false;
    public $event_type_id;
    public $start_date;
    public $start_time;
    public $end_date;
    public $end_time;
    public $location;

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['render', 'contentChange'];

    /**
     * Règles de validation pour l'article et l'événement.
     *
     * @return array
     */
    protected function rules() {
        $rules = [
            'post.title'                      => 'required|string|max:255',
            'post.icon'                       => 'required|string|max:255',
            'post.content'                    => 'required|string',
            'post.rubric_id'                  => 'required',
            'post.published'                  => 'required|boolean',
            'post.is_acknowledgment_required' => 'boolean',
            'post.is_rating_enabled'          => 'boolean',
        ];

        if ($this->attach_to_agenda) {
            $rules['event_type_id'] = 'required|integer|exists:event_types,id';
            $rules['start_date'] = 'required|date';
            $rules['start_time'] = 'nullable';
            $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
            $rules['end_time'] = 'nullable';
            $rules['location'] = 'nullable|string|max:150';
        }

        return $rules;
    }

    /**
     * Met à jour le contenu de l'article lors d'un changement dans l'éditeur.
     *
     * @param string $content Nouveau contenu.
     */
    public function contentChange($content) {
        $this->post->content = $content;
    }

    public function updatedPostRubricId($value) {
        if ($this->mode === 'creation') {
            if ($value) {
                $eventsRubric = Rubric::whereIn('name', ['Evénements', 'Événements', 'Evenements'])->first();
                $eventsRubricId = $eventsRubric ? $eventsRubric->id : 15;
                $rubric = Rubric::find($value);
                if ($rubric && $rubric->parent_id == $eventsRubricId) {
                    $this->attach_to_agenda = true;
                } else {
                    $this->attach_to_agenda = false;
                }
            } else {
                $this->attach_to_agenda = false;
            }
        }
    }

    /**
     * Définit l'article et initialise l'éditeur et les onglets.
     *
     * @param int|null $id Identifiant de l'article.
     */
    public function setPost($id = NULL) {
        if (is_null($id)) {
            $this->emit('deleteContent');
        }

        $this->post = $this->post ?? Post::findOrNew($id);

        if ($this->mode === 'creation') {
            $this->post->published = FALSE;
            $this->post->is_acknowledgment_required = FALSE;
            $this->post->is_rating_enabled = FALSE;
        }

        if ($this->post->exists && $this->post->event) {
            $this->attach_to_agenda = true;
            $this->event_type_id = $this->post->event->event_type_id;
            $this->start_date = $this->post->event->start_date ? $this->post->event->start_date->format('Y-m-d') : null;
            $this->start_time = $this->post->event->start_time ? substr($this->post->event->start_time, 0, 5) : null;
            $this->end_date = $this->post->event->end_date ? $this->post->event->end_date->format('Y-m-d') : null;
            $this->end_time = $this->post->event->end_time ? substr($this->post->event->end_time, 0, 5) : null;
            $this->location = $this->post->event->location;
        } else {
            $this->attach_to_agenda = false;
            $this->event_type_id = null;
            $this->start_date = null;
            $this->start_time = null;
            $this->end_date = null;
            $this->end_time = null;
            $this->location = null;

            if ($this->mode === 'creation' && $this->post->rubric_id) {
                $eventsRubric = Rubric::whereIn('name', ['Evénements', 'Événements', 'Evenements'])->first();
                $eventsRubricId = $eventsRubric ? $eventsRubric->id : 15;
                $rubric = Rubric::find($this->post->rubric_id);
                if ($rubric && $rubric->parent_id == $eventsRubricId) {
                    $this->attach_to_agenda = true;
                }
            }
        }

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

    /**
     * Déclenche l'événement navigateur d'initialisation de TinyMCE.
     */
    public function initTinymce(){
        $this->dispatchBrowserEvent('initTinymce');
    }

    /**
     * Réinitialise les modifications (recharge l'original).
     */
    public function refresh() {
        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Réinitialisation effectuée avec succès."
            ])
            ->self();
    }

    /**
     * Définit l'onglet courant.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
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

    /**
     * Enregistre l'article (création ou modification).
     */
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

        if ($this->attach_to_agenda) {
            $this->post->event()->updateOrCreate([], [
                'event_type_id' => $this->event_type_id,
                'start_date' => $this->start_date,
                'start_time' => $this->start_time ?: null,
                'end_date' => $this->end_date ?: null,
                'end_time' => $this->end_time ?: null,
                'location' => $this->location ?: null,
            ]);
        } else {
            $this->post->event()->delete();
        }

        $this->emit('saveGallery');
    }

    /**
     * Rendu du composant.
     *
     * @param array|null $messageBag Sac de messages d'alerte (optionnel).
     * @return \Illuminate\View\View
     */
    public function render($messageBag = NULL){
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
                'icons' => $this->getMiCodes(),
                'eventTypes' => \App\Models\EventType::orderBy('name', 'ASC')->get(),
            ]);
    }
}
