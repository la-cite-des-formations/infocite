<?php

namespace App\Http\Livewire\Modals\Admin\Users;

use App\App;
use App\CustomFacades\AP;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\User;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $user;
    public $mode;
    public $canAdd = TRUE;
    public $groupType = 'C';
    public $groupSearch = '';
    public $groupsIDs;
    public $selectedUserGroups = [];
    public $selectedAvailableGroups = [];
    public $appSearch = '';
    public $appsIDs;
    public $selectedLinkedApps = [];
    public $selectedAvailableApps = [];
    public $profileSearch = '';
    public $profilesIDs;
    public $selectedProfiles = [];
    public $selectedLinkedProfiles = [];
    public $selectedAvailableProfiles = [];
    public $function = '';
    public $classesMin = 5;
    public $userNbClasses;
    public $truncateClassesList;
    public $formTabs;
    public $groupsTabs;

    protected $listeners = ['render'];

    protected $rules = [
        'user.name' => 'required|string|max:255',
        'user.first_name' => 'required|string|max:255',
        'user.email' => 'nullable|email|max:255',
        'user.password' => 'nullable|string|min:8|max:255',
    ];

    public function setUser($id = NULL) {
        $this->user = $this->user ?? User::findOrNew($id);

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.users',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir l'utilisateur",
                    'hidden' => FALSE,
                ],
                'groups' => [
                    'icon' => 'groups',
                    'title' => "Gérer les groupes de l'utilisateur",
                    'hidden' => !$this->user->id,
                ],
                'apps' => [
                    'icon' => 'view_module',
                    'title' => "Gérer les applications de l'utilisateur",
                    'hidden' => !$this->user->id,
                ],
                'profiles' => [
                    'icon' => 'portrait',
                    'title' => "Associer et appliquer les profils",
                    'hidden' => !$this->user->id,
                ],
            ],
        ];

        $this->groupsTabs = [
            'name' => 'groupsTabs',
            'currentTab' => 'groups',
            'panesPath' => 'includes.admin.users',
            'withMarge' => FALSE,
            'tabs' => [
                'groups' => [
                    'icon' => 'group_add',
                    'title' => "Sélectionner des groupes à associer",
                    'hidden' => FALSE,
                ],
                'function' => [
                    'icon' => 'build',
                    'title' => "Définir une fonction à attribuer",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);
        $this->mode = $mode ?? 'view';
        $this->setUser($id ?? NULL);
        if (!empty($id)) {
            $this->groupsIDs = $this->user->groups->pluck('id');
            $this->appsIDs = $this->user->apps->pluck('id');
            $this->profilesIDs = $this->user->profiles->pluck('id');
        }
        $this->userNbClasses = $this->user->groups(['C', 'E'])->count();
        $this->truncateClassesList = $this->userNbClasses > $this->classesMin;
    }

    public function refresh() {
        $this->user->groups()->sync($this->groupsIDs);
        $this->selectedUserGroups = [];
        $this->selectedAvailableGroups = [];
        $this->updatedSelectedUserGroups();

        $this->user->apps()->sync($this->appsIDs);
        $this->selectedLinkedApps = [];
        $this->selectedAvailableApps = [];

        $this->user->profiles()->sync($this->profilesIDs);
        $this->selectedLinkedProfiles = [];
        $this->selectedAvailableProfiles = [];

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

        $this->selectedAvailableGroups = [];
        $this->selectedUserGroups = [];

        $this->updatedSelectedUserGroups();
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->user = NULL;
        if ($mode !== 'view') $this->setUser();

        $this->selectedAvailableGroups = [];
        $this->selectedUserGroups = [];

        $this->updatedSelectedUserGroups();
    }

    public function switchClasses() {
        $this->truncateClassesList = !$this->truncateClassesList;
    }

    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'profiles' : $this->addSelectedAvailableProfiles();
            return;

            case 'apps' : $this->addSelectedAvailableApps();
            return;

            case 'groups' : $this->addSelectedAvailableGroups();
            return;

            case 'function' : $this->addFunction();
            return;
        }
    }

    public function applyProfiles() {
        $this->selectedProfiles = array_merge($this->selectedAvailableProfiles, $this->selectedLinkedProfiles);

        if ($this->isEmpty('selectedProfiles', "Aucun profil sélectionné")) return;

        $user = $this->user;

        $profiles = User::whereIn('id', $this->selectedProfiles)->get();

        foreach ($profiles as $profile) {
            $profileApps = $profile->apps->pluck('id');

            $profileGroups = $profile
                ->groups()
                ->pluck('function', 'id')
                ->map(function ($function, $id) use ($user) {
                    $existingSystemGroup = $user
                        ->groups(['S'])
                        ->find($id);
                    if ($existingSystemGroup) {
                        $function = sprintf(
                            '%04b',
                            bindec($function) | bindec($existingSystemGroup->pivot->function)
                        );
                    }
                    return ['function' => $function];
                });
            $user
                ->groups()
                ->syncWithoutDetaching($profileGroups);

            $user
                ->apps()
                ->syncWithoutDetaching($profileApps);
        }

        $this->selectedLinkedProfiles = [];
        $this->selectedAvailableProfiles = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Application des profils effectuée avec succès."
            ])
            ->self();
    }

    private function addSelectedAvailableProfiles() {
        if ($this->isEmpty('selectedAvailableProfiles', "Aucun profil sélectionné")) return;

        $this->user
            ->profiles()
            ->syncWithoutDetaching($this->selectedAvailableProfiles);

        $this->selectedLinkedProfiles = $this->selectedAvailableProfiles;
        $this->selectedAvailableProfiles = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Association des profils effectuée avec succès."
            ])
            ->self();
    }

    private function addSelectedAvailableApps() {
        if ($this->isEmpty('selectedAvailableApps', "Aucune application sélectionnée")) return;

        $this->user
            ->apps()
            ->syncWithoutDetaching($this->selectedAvailableApps);

        $this->selectedAvailableApps = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Importation effectuée avec succès."
            ])
            ->self();
    }

    public function addSelectedAvailableGroups() {
        if ($this->isEmpty('selectedAvailableGroups', "Aucun groupe sélectionné")) return;

        $this->user
            ->groups()
            ->syncWithoutDetaching($this->groupType === 'S' ?
                array_fill_keys($this->selectedAvailableGroups, ['function' => '0000']) :
                $this->selectedAvailableGroups
            );

        $this->selectedAvailableGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Association effectuée avec succès."
            ])
            ->self();
    }

    private function addFunction() {
        if ($this->IsEmpty('selectedUserGroups', "Aucun groupe sélectionné") ||
            $this->IsEmpty('function', "Fonction non définie")) return;

        $this->user
            ->groups()
            ->syncWithoutDetaching(
                array_fill_keys($this->selectedUserGroups, [
                    'function' => $this->function
                ])
            );

        $this->selectedUserGroups = [];
        $this->function = '';

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Fonction attribuée avec succès."
            ])
            ->self();
    }

    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'profiles' : $this->removeSelectedLinkedProfiles();
            return;

            case 'apps' : $this->removeSelectedLinkedApps();
            return;

            case 'groups' : $this->removeSelectedUserGroups();
            return;

            case 'function' : $this->removeFunction();
            return;

            case 'roles' : $this->removeRoles();
            return;
        }
    }

    private function removeSelectedLinkedProfiles() {
        if ($this->isEmpty('selectedLinkedProfiles', "Aucun profil sélectionné")) return;

        $this->user
            ->profiles()
            ->detach($this->selectedLinkedProfiles);

        $this->selectedLinkedProfiles = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    private function removeSelectedLinkedApps() {
        if ($this->isEmpty('selectedLinkedApps', "Aucune application sélectionnée")) return;

        $this->user
            ->apps()
            ->detach($this->selectedLinkedApps);

        $this->selectedLinkedApps = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    private function removeSelectedUserGroups() {
        if ($this->isEmpty('selectedUserGroups', "Aucun groupe sélectionné")) return;

        $this->user
            ->groups()
            ->detach($this->selectedUserGroups);

        $this->selectedUserGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Dissociation effectuée avec succès."
            ])
            ->self();
    }

    private function removeFunction() {
        if ($this->isEmpty('selectedUserGroups', "Aucun groupe sélectionné")) return;

        $this->user
            ->groups()
            ->syncWithoutDetaching(array_fill_keys($this->selectedUserGroups, ['function' => NULL]));

        $this->selectedUserGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Fonction retirée avec succès."
            ])
            ->self();
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->user->email = $this->user->email ?: NULL;
        $this->user->password = $this->user->password ?: NULL;

        $this->user->save();

        if ($this->mode == 'creation') {
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création de l'utilisateur effectuée avec succès."
                ])
                ->self();
        }
        else {
            $this->groupsIDs = $this->user->groups->pluck('id');
            $this->appsIDs = $this->user->apps->pluck('id');
            $this->profilesIDs = $this->user->profiles->pluck('id');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification de l'utilisateur effectuée avec succès."
                ])
                ->self();
        }
    }

    private function availableGroups() {
        $search = $this->groupSearch;
        return Group::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->where('type', $this->groupType)
            ->whereNotIn('id', $this->user->groups->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    private function availableApps() {
        $search = $this->appSearch;
        return App::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->whereNotIn('id', $this->user->apps->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    private function availableProfiles() {
        $search = $this->profileSearch;
        return User::query()
            ->when($search, function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%");
            })
            ->where('name', AP::PROFILE)
            ->whereNotIn('id', $this->user->profiles->pluck('id'))
                ->orderByRaw('first_name ASC')
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
            view('livewire.modals.admin.users.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un utilisateur',
                'availableGroups' => $this->availableGroups(),
                'availableApps' => $this->availableApps(),
                'availableProfiles' => $this->availableProfiles(),
            ]);
    }
}
