<?php

namespace App\Http\Livewire\Modals\Admin\Chartnodes;

use App\Format;
use App\Group;
use App\Chartnode;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $chartnode;
    public $createProcessGroup;
    public $newProcessGroup = NULL;
    public $mode;
    public $canAdd = TRUE;
    public $formTabs;

    protected $rules = [
        'chartnode.name' => 'required|string|max:255',
        'chartnode.code_fonction' => 'required|integer',
        'chartnode.parent_id' => 'nullable|integer',
        'chartnode.format_id' => 'required|integer',
        'chartnode.rank' => 'required|string|max:20',
        'newProcessGroup.name' => 'string|max:255',
        'newProcessGroup.type' => 'string|size:1',
    ];

    public function setChartnode($id = NULL) {
        $this->chartnode = $this->chartnode ?? Chartnode::findOrNew($id);

        if (!$this->chartnode->id) {
            $this->chartnode->rank = '-';
            $this->createProcessGroup = FALSE;
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.chartnodes',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le noeud graphique",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setChartnode($id ?? NULL);
    }

    public function refresh() {
        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Réinitialisation effectuée avec succès.",
        ]);
    }

    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode == 'creation') $this->chartnode = NULL;
        if ($mode != 'view') {
            $this->setChartnode();
        }
        else {
            $this->chartnode = Chartnode::find($this->chartnode->id);
        };
    }

    public function save() {
        if ($this->mode === 'view') return;

        if ($this->createProcessGroup) {
            $this->validate(['newProcessGroup.name' => 'required|string|max:255']);
            $this->newProcessGroup->save();
            $this->chartnode->group_id = $this->newProcessGroup->id;
        }

        $this->validate();

        $this->chartnode->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Création du noeud graphique effectuée avec succès.",
            ]);
        }
        else {
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Modification du noeud graphique effectuée avec succès.",
            ]);
        }
    }

    public function updatedCreateProcessGroup() {
        if ($this->createProcessGroup) {
            $this->newProcessGroup = new Group([
                'type' => 'P',
                'name' => $this->chartnode->name,
            ]);
        }
        else {
            $this->newProcessGroup = NULL;
        }
    }

    public function updatedChartnodeParentId() {
        $this->chartnode->rank = $this->chartnode->parent_id ?
            Chartnode::find($this->chartnode->parent_id)->rank.'-' :
            '-';
    }

    public function render()
    {
        return $this->mode === 'view' ?
            view('livewire.modals.admin.chartnodes.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un noeud graphique',
                'groups' => Group::where('type', 'P')->orderBy('name')->get(),
                'parents' => Chartnode::where('id', '!=', $this->chartnode->id)->orderBy('name')->get(),
                'formats' => Format::all(),
            ]);
    }
}
