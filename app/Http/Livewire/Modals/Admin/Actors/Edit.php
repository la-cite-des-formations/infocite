<?php

namespace App\Http\Livewire\Modals\Admin\Actors;

use App\Actor;
use App\User;
use Livewire\Component;
use App\Http\Livewire\WithAlert;

class Edit extends Component
{
    use WithAlert;

    public $user;
    public $manager_id;
    public $mode;
    public $canAdd = FALSE;
    public $formTabs;

    protected $rules = [
        'manager_id' => 'nullable',
    ];

    public function setActor($id) {
        $this->user = User::find($id);

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.actors',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le lien hiérarchique",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setActor($id);
        if ($actor = Actor::find($id)) {
            $this->manager_id = $actor->manager_id;
        }
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
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $actor = Actor::findOrNew($this->user->id);
        $actor->id = $actor->id ?? $this->user->id;

        if ($this->manager_id) {
            $actor->manager_id = $this->manager_id;
            $actor->save();
        }
        else {
            $actor->delete();
        }

        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Lien hiérarchique modifié avec succès.",
        ]);
    }

    public function render()
    {
        return $this->mode === 'view' ?
            view('livewire.modals.admin.actors.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un processus fonctionnel',
                'managers' => Actor::getManagers(),
            ]);
    }
}
