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
    public $mode;
    public $canAdd = TRUE;
    public $formTabs;

    protected $rules = [
        'chartnode.name' => 'required|string|max:255',
        'chartnode.code_fonction' => 'nullable|integer',
        'chartnode.parent_id' => 'nullable|integer',
        'chartnode.format_id' => 'required|integer',
        'chartnode.rank' => 'required|string|max:20',
    ];

    public function setChartnode($id = NULL) {
        $this->chartnode = $this->chartnode ?? Chartnode::findOrNew($id);

        if (!$this->chartnode->id) {
            $this->chartnode->rank = '-';
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.chartnodes',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le nœud graphique",
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

    public function drawChartnode() {
        $options = [
            'allowCollapse' => TRUE,
            'allowHtml'=> TRUE,
            'size'=> 'small',
            'compactRows' => TRUE,
        ];

        $this->emit('drawOrgChart', 'orgchart', Chartnode::getOrgChart($this->chartnode), $options);
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode == 'creation') $this->chartnode = NULL;
        if ($mode != 'view') {
            $this->setChartnode();
        }
        else {
            $this->chartnode = Chartnode::find($this->chartnode->id);
            $this->drawChartnode();
        };
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->chartnode->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Création du nœud graphique effectuée avec succès.",
            ]);
        }
        else {
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Modification du nœud graphique effectuée avec succès.",
            ]);
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
                'addButtonTitle' => 'Ajouter un nœud graphique',
                'groups' => Group::where('type', 'P')->orderBy('name')->get(),
                'parents' => Chartnode::where('id', '!=', $this->chartnode->id)->orderBy('name')->get(),
                'formats' => Format::all(),
            ]);
    }
}
