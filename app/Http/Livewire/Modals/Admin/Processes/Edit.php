<?php

namespace App\Http\Livewire\Modals\Admin\Processes;

use App\Actor;
use App\Format;
use App\Group;
use App\Process;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $process;
    public $createProcessGroup;
    public $newProcessGroup = NULL;
    public $mode;
    public $canAdd = TRUE;
    public $formTabs;

    protected $rules = [
        'process.name' => 'required|string|max:255',
        'process.group_id' => 'required|integer',
        'process.parent_id' => 'nullable|integer',
        'process.manager_id' => 'nullable|integer',
        'process.format_id' => 'required|integer',
        'process.rank' => 'required|string|max:20',
        'newProcessGroup.name' => 'string|max:255',
        'newProcessGroup.type' => 'string|size:1',
    ];

    public function setProcess($id = NULL) {
        $this->process = $this->process ?? Process::findOrNew($id);

        if (!$this->process->id) {
            $this->process->rank = '-';
            $this->createProcessGroup = FALSE;
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.processes',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le processus fonctionnel",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setProcess($id ?? NULL);
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

        if ($mode == 'creation') $this->process = NULL;
        if ($mode != 'view') {
            $this->setProcess();
        }
        else {
            $this->process = Process::find($this->process->id);
        };
    }

    public function save() {
        if ($this->mode === 'view') return;

        if ($this->createProcessGroup) {
            $this->validate(['newProcessGroup.name' => 'required|string|max:255']);
            $this->newProcessGroup->save();
            $this->process->group_id = $this->newProcessGroup->id;
        }

        $this->validate();

        if ($this->process->manager_id && is_null(Actor::find($this->process->manager_id))) {
            // le manager n'existe pas en tant qu'acteur, il faut le définir
            (new Actor([
                'id' => $this->process->manager_id
            ]))->save();
        }

        $this->process->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Création du processus fonctionnel effectuée avec succès.",
            ]);
        }
        else {
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Modification du processus fonctionnel effectuée avec succès.",
            ]);
        }
    }

    public function updatedCreateProcessGroup() {
        if ($this->createProcessGroup) {
            $this->newProcessGroup = new Group([
                'type' => 'P',
                'name' => $this->process->name,
            ]);
        }
        else {
            $this->newProcessGroup = NULL;
        }
    }

    public function updatedProcessGroupId() {
        $this->render();
    }

    public function updatedProcessParentId() {
        $this->process->rank = $this->process->parent_id ?
            Process::find($this->process->parent_id)->rank.'-' :
            '-';
        $this->render();
    }

    private function getManagers() {
        $managers = [];

        if ($this->process->group_id) {
            $managers = $this->process->actors;
        }

        if ($this->process->parent_id) {
            $managers = $managers->merge($this->process->parent->actors);

            if ($this->process->parent->parent_id) {
                $managers = $managers->merge($this->process->parent->parent->actors);
            }
        }

        return $managers;
    }

    public function render()
    {
        return $this->mode === 'view' ?
            view('livewire.modals.admin.processes.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un processus fonctionnel',
                'groups' => Group::where('type', 'P')->orderBy('name')->get(),
                'managers' => $this->getManagers(),
                'parents' => Process::where('id', '!=', $this->process->id)->orderBy('name')->get(),
                'formats' => Format::all(),
            ]);
    }
}
