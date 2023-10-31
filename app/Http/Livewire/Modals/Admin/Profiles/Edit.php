<?php

namespace App\Http\Livewire\Modals\Admin\Profiles;

use App\App;
use App\CustomFacades\AP;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\User;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $profile;
    public $mode;
    public $canAdd = TRUE;
    public $groupType = 'C';
    public $groupSearch = '';
    public $groupsIDs;
    public $selectedLinkedGroups = [];
    public $selectedAvailableGroups = [];
    public $appSearch = '';
    public $appsIDs;
    public $selectedLinkedApps = [];
    public $selectedAvailableApps = [];
    public $userSearch = '';
    public $usersIDs;
    public $selectedLinkedUsers = [];
    public $selectedAvailableUsers = [];
    public $function = '';
    public $formTabs;
    public $groupsTabs;

    protected $listeners = ['render'];

    protected $rules = [
        'profile.name' => 'required|string|max:255',
        'profile.first_name' => 'required|string|max:255',
    ];

    public function setProfile($id = NULL) {
        $this->profile = $this->profile ?? User::findOrNew($id);
        if (!$id) {
            $this->profile->name = AP::PROFILE;
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.profiles',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le profil",
                    'hidden' => FALSE,
                ],
                'groups' => [
                    'icon' => 'groups',
                    'title' => "Gérer les groupes associés au profil",
                    'hidden' => !$this->profile->id,
                ],
                'users' => [
                    'icon' => 'person',
                    'title' => "Gérer les utilisateurs associés au profil",
                    'hidden' => !$this->profile->id,
                ],
                'apps' => [
                    'icon' => 'view_module',
                    'title' => "Gérer les applications associées au profil",
                    'hidden' => !$this->profile->id,
                ],
            ],
        ];

        $this->groupsTabs = [
            'name' => 'groupsTabs',
            'currentTab' => 'groups',
            'panesPath' => 'includes.admin.profiles',
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
        $this->setProfile($id ?? NULL);
        if (!empty($id)) {
            $this->groupsIDs = $this->profile->groups->pluck('id');
            $this->appsIDs = $this->profile->apps->pluck('id');
            $this->usersIDs = $this->profile->users->pluck('id');
        }
    }

    public function refresh() {
        $this->profile->groups()->sync($this->groupsIDs);
        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];
        $this->updatedSelectedLinkedGroups();

        $this->profile->apps()->sync($this->appsIDs);
        $this->selectedLinkedApps = [];
        $this->selectedAvailableApps = [];

        $this->profile->users()->sync($this->appsIDs);
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

        $this->updatedSelectedLinkedGroups();
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->profile = NULL;
        if ($mode !== 'view') $this->setProfile();

        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];

        $this->updatedSelectedLinkedGroups();
    }

    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'profile' : $this->addSelectedAvailableUsersAndApllyProfile();
            return;

            case 'apps' : $this->addSelectedAvailableApps();
            return;

            case 'groups' : $this->addSelectedAvailableGroups();
            return;

            case 'function' : $this->addFunction();
            return;
        }
    }

    public function applyProfile() {
        if ($this->isEmpty('selectedLinkedUsers', "Aucun utilisateur sélectionné")) return;

        $profileApps = $this->profile->apps->pluck('id');

        $users = User::whereIn('id', $this->selectedLinkedUsers)->get();

        foreach ($users as $user) {
            $profileGroups = $this->profile->groups->pluck('function', 'id')
                ->map(function ($function) {
                    return ['function' => $function];
                });

            $user
                ->apps()
                ->syncWithoutDetaching($profileApps);

            $user
                ->groups()
                ->syncWithoutDetaching($profileGroups);
        }

        $this->selectedLinkedUsers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Application du profil effectuée avec succès."
            ])
            ->self();
    }

    private function addSelectedAvailableUsersAndApllyProfile() {
        if ($this->isEmpty('selectedAvailableUsers', "Aucun utilisateur sélectionné")) return;

        $this->profile
            ->users()
            ->syncWithoutDetaching($this->selectedAvailableUsers);

        $this->selectedLinkedUsers = $this->selectedAvailableUsers;

        $this->selectedAvailableUsers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Association et application du profil effectuée avec succès."
            ])
            ->self();
    }

    private function addSelectedAvailableApps() {
        if ($this->isEmpty('selectedAvailableApps', "Aucune application sélectionnée")) return;

        $this->profile
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

        $this->profile
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
        if ($this->IsEmpty('selectedLinkedGroups', "Aucun groupe sélectionné") ||
            $this->IsEmpty('function', "Fonction non définie")) return;

        $this->profile
            ->groups()
            ->syncWithoutDetaching(
                array_fill_keys($this->selectedLinkedGroups, [
                    'function' => $this->function
                ])
            );

        $this->selectedLinkedGroups = [];
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
            case 'profile' : $this->removeSelectedLinkedUsers();
            return;

            case 'apps' : $this->removeSelectedLinkedApps();
            return;

            case 'groups' : $this->removeSelectedLinkedGroups();
            return;

            case 'function' : $this->removeFunction();
            return;
        }
    }

    private function removeSelectedLinkedUsers() {
        if ($this->isEmpty('selectedLinkedUsers', "Aucun utilisateur sélectionné")) return;

        $this->profile
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

    private function removeSelectedLinkedApps() {
        if ($this->isEmpty('selectedLinkedApps', "Aucune application sélectionnée")) return;

        $this->profile
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

    private function removeSelectedLinkedGroups() {
        if ($this->isEmpty('selectedLinkedGroups', "Aucun groupe sélectionné")) return;

        $this->profile
            ->groups()
            ->detach($this->selectedLinkedGroups);

        $this->selectedLinkedGroups = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Dissociation effectuée avec succès."
            ])
            ->self();
    }

    private function removeFunction() {
        if ($this->isEmpty('selectedLinkedGroups', "Aucun groupe sélectionné")) return;

        $this->profile
            ->groups()
            ->syncWithoutDetaching(array_fill_keys($this->selectedLinkedGroups, ['function' => NULL]));

        $this->selectedLinkedGroups = [];

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

        $this->profile
            ->save();

        if ($this->mode == 'creation') {
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création du profil effectuée avec succès."
                ])
                ->self();
        }
        else {
            $this->groupsIDs = $this->profile->groups->pluck('id');
            $this->appsIDs = $this->profile->apps->pluck('id');
            $this->usersIDs = $this->profile->users->pluck('id');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification du profil effectuée avec succès."
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
            ->whereNotIn('id', $this->profile->groups->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    private function availableApps() {
        $search = $this->appSearch;
        return App::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->whereNotIn('id', $this->profile->apps->pluck('id'))
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
            ->whereNotIn('id', $this->profile->users->pluck('id'))
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
            view('livewire.modals.admin.profiles.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un profil',
                'availableGroups' => $this->availableGroups(),
                'availableApps' => $this->availableApps(),
                'availableUsers' => $this->availableUsers(),
            ]);
    }
}
