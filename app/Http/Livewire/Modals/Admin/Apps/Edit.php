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
    public $canAdd;
    public $groupType = 'C';
    public $groupsIDs;
    public $selectedLinkedGroups = [];
    public $selectedAvailableGroups = [];
    public $groupSearch = '';
    public $usersIDs;
    public $selectedLinkedUsers = [];
    public $selectedAvailableUsers = [];
    public $userSearch = '';
    public $profilesIDs;
    public $selectedLinkedProfiles = [];
    public $selectedAvailableProfiles = [];
    public $profileSearch = '';
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

    private function setApp($id = NULL) {
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
                    'title' => "Associer des groupes",
                    'hidden' => !$this->app->id,
                ],
                'profiles' => [
                    'icon' => 'portrait',
                    'title' => "Associer des profils",
                    'hidden' => !$this->app->id,
                ],
                'users' => [
                    'icon' => 'person',
                    'title' => "Associer des utilisateurs",
                    'hidden' => !$this->app->id,
                ],
            ],
        ];
    }

    private function setIDs() {
        $this->groupsIDs = $this->app->groups->pluck('id');
        $this->profilesIDs = $this->app->profiles->pluck('id');
        $this->usersIDs = $this->app->realUsers->pluck('id');
    }

    private function resetSelections() {
        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];
        $this->selectedLinkedProfiles = [];
        $this->selectedAvailableProfiles = [];
        $this->selectedLinkedUsers = [];
        $this->selectedAvailableUsers = [];
    }

    public function mount($data) {
        extract($data);

        $currentUser = auth()->user();

        $this->canAdd = $currentUser->can('create', App::class) || $currentUser->can('createFor', App::class);

        if ($currentUser->can('createFor', App::class) && $currentUser->cant('create', App::class)) {
            $this->rules['app.owner_id'] = 'required|integer';
        }

        $this->mode = $mode ?? 'view';
        $this->setApp($id ?? NULL);
        if (!empty($id)) {
            $this->setIDs();
        }
    }

    public function refresh() {
        $this->app->groups()->sync($this->groupsIDs);
        $this->app->profiles()->sync($this->profilesIDs);
        $this->app->realUsers()->sync($this->usersIDs);

        $this->resetSelections();

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

        $this->resetSelections();
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->app = NULL;
        if ($mode !== 'view') $this->setApp();

        $this->resetSelections();
    }

    public function add() {
        switch($this->formTabs['currentTab']) {
            case  'groups' : $this->addGroups();
            return;

            case 'profiles' : $this->addUsers('profile');
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

    private function addUsers($usersType = 'real') {
        if (
            $usersType == 'real' && $this->isEmpty('selectedAvailableUsers', "Aucun utilisateur sélectionné") ||
            $usersType == 'profile' && $this->isEmpty('selectedAvailableProfiles', "Aucun profil sélectionné")
        ) return;

            $selectedUsers = $usersType == 'real' ? $this->selectedAvailableUsers : $this->selectedAvailableProfiles;

        $this->app
            ->users()
            ->syncWithoutDetaching($selectedUsers);

            $this->selectedAvailableProfiles = [];
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

            case 'profiles' : $this->removeUsers('profile');
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

    private function removeUsers($usersType = 'real') {
        if (
            $usersType == 'profile' && $this->isEmpty('selectedLinkedProfiles', "Aucun profil sélectionné") ||
            $usersType == 'real' && $this->isEmpty('selectedLinkedUsers', "Aucun utilisateur sélectionné")
        ) return;

        $selectedUsers = $usersType == 'real' ? $this->selectedLinkedUsers : $this->selectedLinkedProfiles;

        $this->app
            ->users()
            ->detach($selectedUsers);

        $this->selectedLinkedUsers = [];
        $this->selectedLinkedProfiles = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->app->favicon = Http::get("https://www.google.com/s2/favicons?domain=" . $this->app->url)->header("Content-Location");

        $this->validate();

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
            $this->setIDs();

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification de l'application effectuée avec succès."
                ])
                ->self();
        }
    }

    private function availableGroups() {
        $search = $this->groupSearch;
        return Group::query()
            ->where('type', $this->groupType)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->whereNotIn('id', $this->app->groups->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    private function availableProfiles() {
        $search = $this->profileSearch;
        return User::query()
            ->where('name', AP::PROFILE)
            ->when($search, function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%");
            })
            ->whereNotIn('id', $this->app->profiles->pluck('id'))
                ->orderBy('first_name')
                ->get();
    }

    private function availableUsers() {
        $search = $this->userSearch;
        return User::query()
            ->where('name', '<>', AP::PROFILE)
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', name) LIKE ?", "%{$search}%");
            })
            ->whereNotIn('id', $this->app->realUsers->pluck('id'))
                ->orderByRaw('name ASC, first_name ASC')
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
            view('livewire.modals.admin.apps.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter une application',
                'availableGroups' => $this->availableGroups(),
                'availableProfiles' => $this->availableProfiles(),
                'availableUsers' => $this->availableUsers(),
                'users' => User::query()
                    ->where('name', '<>', AP::PROFILE)
                    ->where('is_frozen', 0)
                    ->get(),
                'icons' => $this->getMiCodes(),
            ]);
    }
}
