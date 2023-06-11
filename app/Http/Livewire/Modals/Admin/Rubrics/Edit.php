<?php

namespace App\Http\Livewire\Modals\Admin\Rubrics;

use App\Rubric;
use App\Group;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $rubric;
    public $mode;
    public $canAdd = TRUE;
    public $formTabs;
    public $groupType = 'C';
    public $groupSearch;
    public $selectedLinkedGroups = [];
    public $selectedAvailableGroups = [];

    protected $listeners = ['render'];

    protected $rules = [
        'rubric.name' => 'required|string|max:255',
        'rubric.title' => 'required|string|max:255',
        'rubric.description' => 'nullable|string',
        'rubric.icon' => 'nullable|string|max:255',
        'rubric.is_parent' => 'required|boolean',
        'rubric.parent_id' => 'nullable|numeric',
        'rubric.position' => 'required',
        'rubric.rank' => 'required|numeric',
        'rubric.contains_posts' => 'required|boolean',
        'rubric.segment' => 'required|string',
        'rubric.view' => 'nullable|string',
    ];

    public function setRubric($id = NULL) {
        $this->rubric = $this->rubric ?? Rubric::findOrNew($id);

        if ($this->mode === 'creation') {
            $this->rubric->is_parent = FALSE;
            $this->rubric->contains_posts = TRUE;
        }

        if ($id && $this->rubric->parent_id) {
            $this->rubric->rank = explode('-', $this->rubric->rank)[1];
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.rubrics',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir la rubrique",
                    'hidden' => FALSE,
                ],
                'groups' => [
                    'icon' => 'groups',
                    'title' => "Associer des groupes",
                    'hidden' => !$this->rubric->id,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setRubric($id ?? NULL);
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

        if ($mode === 'creation') $this->rubric = NULL;
        if ($mode !== 'view') $this->setRubric();
    }

    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'groups' : $this->addSelectedAvailableGroups();
            return;
        }
    }

    private function addSelectedAvailableGroups() {
        if ($this->isEmpty('selectedAvailableGroups', "Aucun groupe sélectionné")) return;

        $this->rubric
            ->groups()
            ->syncWithoutDetaching($this->selectedAvailableGroups);

        $this->selectedAvailableGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Association effectuée avec succès."
            ])
            ->self();
    }

    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'groups' : $this->removeSelectedLinkedGroups();
            return;
        }
    }

    private function removeSelectedLinkedGroups() {
        if ($this->isEmpty('selectedLinkedGroups', "Aucun groupe sélectionné")) return;

        $this->rubric
            ->groups()
            ->detach($this->selectedLinkedGroups);

        $this->selectedLinkedGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->rubric->parent_id = $this->rubric->parent_id ?: NULL;
        $this->validate();

        if ($this->rubric->is_parent) {
            foreach ($this->rubric->childs as $childRubric) {
                $rank = explode('-', $childRubric->rank);
                $rank[0] = $this->rubric->rank;
                $childRubric->rank = implode('-', $rank);
                $childRubric->save();
            }
        }

        $rank = $this->rubric->rank;

        if ($this->rubric->parent_id) {
            $this->rubric->rank = "{$this->rubric->parent->rank}-{$rank}";
        }

        $this->rubric
            ->save();

        $this->rubric->rank = $rank;

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création de la rubrique effectuée avec succès."
                ])
                ->self();
        }
        else {
            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification de la rubrique effectuée avec succès."
                ])
                ->self();
        }
    }

    private function availableGroups() {
        return Group::query()
            ->where('type', $this->groupType)
            ->whereNotIn('id', $this->rubric->groups->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    public function render($messageBag = NULL)
    {
        if ($messageBag) {
            extract($messageBag);
            session()->flash('alertClass', $alertClass);
            session()->flash('message', $message);
        }

        return $this->mode === 'view' ?
            view('livewire.modals.admin.rubrics.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter une rubrique',
                'rubrics' => Rubric::filter([
                    'search' => '',
                    'is_parent' => TRUE
                ])->get(),
                'availableGroups' => $this->availableGroups(),
            ]);
    }
}
