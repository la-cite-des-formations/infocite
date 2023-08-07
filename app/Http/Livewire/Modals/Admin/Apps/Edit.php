<?php

namespace App\Http\Livewire\Modals\Admin\Apps;

use App\App;
use App\CustomFacades\AP;
use App\Group;
use App\User;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;
    use WithIconpicker;

    public $app;
    public $mode;
    public $canAdd = TRUE;
    public $groupType = 'C';
    public $groupsIDs;
    public $selectedLinkedGroups = [];
    public $selectedAvailableGroups = [];
    public $userSearch = '';
    public $usersIDs;
    public $selectedLinkedUsers = [];
    public $selectedAvailableUsers = [];
    public $formTabs;

    protected $listeners = ['render'];

    protected $rules = [
        'app.owner_id' => 'nullable|integer',
        'app.auth_type' => 'required',
        'app.name' => 'required|string|max:255',
        'app.description' => 'nullable|string',
        'app.icon' => 'nullable|string|max:20',
        'app.url' => 'required|url|max:255',
    ];

    public function setApp($id = NULL) {
        $this->app = $this->app ?? App::findOrNew($id);

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.apps',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir l'application",
                    'hidden' => FALSE,
                ],
                'groups' => [
                    'icon' => 'groups',
                    'title' => "Gérer les groupes utilisateurs",
                    'hidden' => !$this->app->id,
                ],
                'users' => [
                    'icon' => 'person',
                    'title' => "Gérer les utilisateurs spécifiques",
                    'hidden' => !$this->app->id,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setApp($id ?? NULL);
        if (!empty($id)) {
            $this->groupsIDs = $this->app->groups->pluck('id');
            $this->usersIDs = $this->app->users->pluck('id');
        }
    }

    public function refresh() {
        $this->app->groups()->sync($this->groupsIDs);
        $this->app->users()->sync($this->usersIDs);

        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];
        $this->selectedLinkedUsers = [];
        $this->selectedAvailableUsers = [];

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

        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];
        $this->selectedLinkedUsers = [];
        $this->selectedAvailableUsers = [];
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->app = NULL;
        if ($mode !== 'view') $this->setApp();

        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];
        $this->selectedLinkedUsers = [];
        $this->selectedAvailableUsers = [];
    }

    public function add() {
        switch($this->formTabs['currentTab']) {
            case  'groups' : $this->addGroups();
            return;

            case 'users' : $this->addUsers();
            return;
        }
    }

    private function addGroups() {
        if ($this->isEmpty('selectedAvailableGroups', "Aucun groupe sélectionné")) return;

        $this->app
            ->groups()
            ->syncWithoutDetaching($this->selectedAvailableGroups);

        $this->selectedAvailableGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Importation effectuée avec succès."
            ])
            ->self();
    }

    private function addUsers() {
        if ($this->isEmpty('selectedAvailableUsers', "Aucun utilisateur sélectionné")) return;

        $this->app
            ->users()
            ->syncWithoutDetaching($this->selectedAvailableUsers);

        $this->selectedAvailableUsers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Importation effectuée avec succès."
            ])
            ->self();
    }

    public function remove() {
        switch($this->formTabs['currentTab']) {
            case  'groups' : $this->removeGroups();
            return;

            case 'users' : $this->removeUsers();
            return;
        }
    }

    private function removeGroups() {
        if ($this->isEmpty('selectedLinkedGroups', "Aucun groupe sélectionné")) return;

        $this->app
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

    private function removeUsers() {
        if ($this->isEmpty('selectedLinkedUsers', "Aucun utilisateur sélectionné")) return;

        $this->app
            ->users()
            ->detach($this->selectedLinkedUsers);

        $this->selectedLinkedUsers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    public function save() {
        $this->app->favicon = Http::get("https://www.google.com/s2/favicons?domain=" . $this->app->url)->header("Content-Location");
        if ($this->mode === 'view') return;

        $this->validate();

        $this->app->owner_id = $this->app->owner_id ?: NULL;

        $this->app->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création de l'application effectuée avec succès."
                ])
                ->self();
        }
        else {
            $this->groupsIDs = $this->app->groups->pluck('id');
            $this->usersIDs = $this->app->users->pluck('id');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification de l'application effectuée avec succès."
                ])
                ->self();
        }
    }

    private function availableGroups() {
        return Group::query()
            ->where('type', $this->groupType)
            ->whereNotIn('id', $this->app->groups->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    private function availableUsers() {
        $search = $this->userSearch;
        return User::query()
            ->where('name', '<>', AP::PROFILE)
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', name) LIKE ?", "%{$search}%");
            })
            ->whereNotIn('id', $this->app->users->pluck('id'))
                ->orderByRaw('name ASC, first_name ASC')
                ->get();
    }

    public function render($messageBag = NULL)
    {
        $searchIcons = $this->searchIcons;
        if ($messageBag) {
            extract($messageBag);
            session()->flash('alertClass', $alertClass);
            session()->flash('message', $message);
        }

        return $this->mode === 'view' ?
            view('livewire.modals.admin.apps.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter une application',
                'availableGroups' => $this->availableGroups(),
                'availableUsers' => $this->availableUsers(),
                'users' => User::query()
                    ->where('name', '<>', AP::PROFILE)
                    ->where('is_frozen', 0)
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
