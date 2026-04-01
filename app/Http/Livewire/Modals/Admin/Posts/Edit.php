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

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['render', 'contentChange'];
    /**
     * Règles de validation pour l'article.
     *
     * @var array
     */
    protected $rules = [
        'post.title'                      => 'required|string|max:255',
        'post.icon'                       => 'required|string|max:255',
        'post.content'                    => 'required|string',
        'post.rubric_id'                  => 'required',
        'post.published'                  => 'required|boolean',
        'post.is_acknowledgment_required' => 'boolean',
    ];

    /**
     * Met à jour le contenu de l'article lors d'un changement dans l'éditeur.
     *
     * @param string $content Nouveau contenu.
     */
    public function contentChange($content) {
        $this->post->content = $content;
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
            ]);
    }
}
