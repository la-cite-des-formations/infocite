<?php

namespace App\Http\Livewire\Modals\Admin\Groups;

use App\App;
use App\CustomFacades\AP;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\Roles;
use App\User;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $group;
    public $mode;
    public $canAdd = TRUE;
    public $function = '';
    public $rolesCheckboxes;
    public $membersIDs;
    public $selectedMembers = [];
    public $selectedAvailableUsers = [];
    public $appSearch = '';
    public $appsIDs;
    public $selectedLinkedApps = [];
    public $selectedAvailableApps = [];
    public $formTabs;
    public $membersTabs;

    protected $listeners = ['render'];

    protected $rules = [
        'group.name' => 'required|string|max:255',
        'group.type' => 'required',
        'function' => 'string|max:255',
    ];

    public function setGroup($id = NULL) {
        $this->group = $this->group ?? Group::findOrNew($id);

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.groups',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le groupe",
                    'hidden' => FALSE,
                ],
                'members' => [
                    'icon' => 'groups',
                    'title' => "Gérer les membres du groupe",
                    'hidden' => !$this->group->id,
                ],
                'apps' => [
                    'icon' => 'view_module',
                    'title' => "Gérer les applications du groupe",
                    'hidden' => !$this->group->id,
                ],
            ],
        ];

        $this->membersTabs = [
            'name' => 'membersTabs',
            'currentTab' => 'members',
            'panesPath' => 'includes.admin.groups',
            'withMarge' => FALSE,
            'tabs' => [
                'members' => [
                    'icon' => 'group_add',
                    'title' => "Sélectionner des utilisateurs à ajouter",
                    'hidden' => FALSE,
                ],
                'function' => [
                    'icon' => 'build',
                    'title' => "Définir une fonction à attribuer",
                    'hidden' => $this->group->type === 'S',
                ],
                'roles' => [
                    'icon' => 'theater_comedy',
                    'title' => "Choisir des rôles à attribuer",
                    'hidden' => $this->group->type !== 'S',
                ],
            ],
        ];
    }

    public function updatedGroupType() {
        $this->membersTabs['tabs']['function']['hidden'] = $this->group->type === 'S';
        $this->membersTabs['tabs']['roles']['hidden'] = $this->group->type !== 'S';

        if ($this->membersTabs['currentTab'] === 'function' &&
            $this->membersTabs['tabs']['function']['hidden']) $this->membersTabs['currentTab'] = 'roles';

        if ($this->membersTabs['currentTab'] === 'roles' &&
            $this->membersTabs['tabs']['roles']['hidden']) $this->membersTabs['currentTab'] = 'function';
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setGroup($id ?? NULL);
        if (!empty($id)) {
            $this->membersIDs = $this->group->users->pluck('id');
            $this->appsIDs = $this->group->apps->pluck('id');
        }
    }

    public function refresh() {
        $this->group->users()->sync($this->membersIDs);
        $this->selectedMembers = [];
        $this->selectedAvailableUsers = [];
        $this->updatedSelectedMembers();

        $this->group->apps()->sync($this->appsIDs);
        $this->selectedLinkedApps = [];
        $this->selectedAvailableApps = [];

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

        $this->selectedAvailableUsers = [];
        $this->selectedMembers = [];

        $this->updatedSelectedMembers();
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->group = NULL;
        if ($mode !== 'view') $this->setGroup();

        $this->selectedMembers = [];
        $this->selectedAvailableUsers = [];

        $this->updatedSelectedMembers();
    }

    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'apps' : $this->addSelectedAvailableApps();
            return;

            case  'members' : $this->addSelectedAvailableUsers();
            return;

            case 'function' : $this->addFunction();
            return;

            case 'roles' : $this->addRoles();
            return;
        }
    }

    private function addSelectedAvailableApps() {
        if ($this->isEmpty('selectedAvailableApps', "Aucune application sélectionnée")) return;

        $this->group
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

    private function addSelectedAvailableUsers() {
        if ($this->isEmpty('selectedAvailableUsers', "Aucun utilisateur sélectionné")) return;

        $this->group
            ->users()
            ->syncWithoutDetaching($this->group->type === 'S' ?
                array_fill_keys($this->selectedAvailableUsers, ['function' => '0000']) :
                $this->selectedAvailableUsers
            );

        $this->selectedAvailableUsers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Importation effectuée avec succès."
            ])
            ->self();
    }

    private function addFunction() {
        if ($this->isEmpty('selectedMembers', "Aucun membre sélectionné") ||
            $this->isEmpty('function', "Fonction non définie")) return;

        $this->group
            ->users()
            ->syncWithoutDetaching(
                array_fill_keys($this->selectedMembers, [
                    'function' => $this->function
                ])
            );

        $this->selectedMembers = [];
        $this->function = '';

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Fonction attribuée avec succès."
            ])
            ->self();
    }

    private function addRoles() {
        if ($this->isEmpty('selectedMembers', "Aucun membre sélectionné")) return;

        $unassignedRolesFlag = Roles::ALL;
        $assignedRolesFlag = Roles::NONE;

        foreach (Roles::all()->collection as $role) {
            if ($this->rolesCheckboxes[$role->id] === FALSE) $unassignedRolesFlag ^= $role->flag;
            if ($this->rolesCheckboxes[$role->id] === TRUE) $assignedRolesFlag |= $role->flag;
        }

        foreach($this->group->users()->whereIn('id', $this->selectedMembers)->get() as $user) {
            $this->group
                ->users()
                ->updateExistingPivot(
                    $user->id,
                    ['function' => sprintf(
                        '%04b',
                        bindec($user->pivot->function) & $unassignedRolesFlag | $assignedRolesFlag
                    )]
                );
        }

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Rôles attribués avec succès."
            ])
            ->self();
    }

    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'apps' : $this->removeSelectedLinkedApps();
            return;

            case 'members' : $this->removeSelectedMembers();
            return;

            case 'function' : $this->removeFunction();
            return;

            case 'roles' : $this->removeRoles();
            return;
        }
    }

    private function removeSelectedLinkedApps() {
        if ($this->isEmpty('selectedLinkedApps', "Aucune application sélectionnée")) return;

        $this->group
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

    private function removeSelectedMembers() {
        if ($this->isEmpty('selectedMembers', "Aucun membre sélectionné")) return;

        $this->group
            ->users()
            ->detach($this->selectedMembers);

        $this->selectedMembers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    private function removeFunction() {
        if ($this->isEmpty('selectedMembers', "Aucun membre sélectionné")) return;

        $this->group
            ->users()
            ->syncWithoutDetaching(array_fill_keys($this->selectedMembers, ['function' => NULL]));

        $this->selectedMembers = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Fonction retirée avec succès."
            ])
            ->self();
    }

    private function removeRoles() {
        if ($this->isEmpty('selectedMembers', "Aucun membre sélectionné")) return;

        $this->group
            ->users()
            ->syncWithoutDetaching(array_fill_keys($this->selectedMembers, ['function' => '0000']));

        $this->selectedMembers = [];

        $this->updatedSelectedMembers();

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Rôles retirés avec succès."
            ])
            ->self();
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->group
            ->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création du groupe effectuée avec succès."
                ])
                ->self();
        }
        else {
            $this->membersIDs = $this->group->users->pluck('id');
            $this->appsIDs = $this->group->apps->pluck('id');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification du groupe effectuée avec succès."
                ])
                ->self();
        }
    }

    public function updatedSelectedMembers() {
        if ($this->membersTabs['currentTab'] !== 'roles') return;

        if (!empty($this->selectedMembers)) {
            $rolesFlags = array_map(
                function ($rolesFlag) { return bindec($rolesFlag); },
                $this->group
                    ->users()
                    ->whereIn('id', $this->selectedMembers)
                    ->pluck('function')->toArray()
            );

            foreach (Roles::all()->collection as $role) {
                // strictement coché  => TRUE ; strictement décoché => FALSE
                $this->rolesCheckboxes[$role->id] = $rolesFlags[0] & $role->flag ? TRUE : FALSE;

                for ($i = 1;
                    $i < count($rolesFlags) && !(($rolesFlags[$i] ^ $rolesFlags[$i-1]) & $role->flag);
                    $i++
                );
                // indéterminé => NULL
                if ($i < count($rolesFlags)) $this->rolesCheckboxes[$role->id] = NULL;
            }
        }
        else $this->rolesCheckboxes = array_fill_keys(Roles::all()->collection->pluck('id')->toArray(), FALSE);

        $this->emit('setIndeterminateCbx', 'member', $this->rolesCheckboxes);
    }

    private function availableApps() {
        $search = $this->appSearch;
        return App::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->whereNotIn('id', $this->group->apps->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    private function availableUsers() {
        return User::query()
            ->where('name', '<>', AP::PROFILE)
            ->when($this->group->type !== 'S', function ($users) {
                $users->where('is_frozen', FALSE);
            })
            ->when($this->group->type === 'C', function ($users) {
                $users->where('is_staff', FALSE);
            })
            ->when($this->group->type === 'P', function ($users) {
                $users->where('is_staff', TRUE);
            })
            ->when($this->group->type === 'E', function ($users) {
                $users
                    ->whereIn(
                        'id',
                        Group::where('type', 'P')
                            ->firstWhere('name', 'formateurs')
                            ->users->pluck('id')
                    )
                    ->orWhereIn(
                        'id',
                        Group::where('type', 'P')
                            ->firstWhere('name', 'educatif')
                            ->users->pluck('id')
                    )
                    ->orWhereIn(
                        'id',
                        Group::where('type', 'P')
                            ->firstWhere('name', 'CFAS')
                            ->users->pluck('id')
                    );
            })
            ->whereNotIn('id', $this->group->users->pluck('id'))
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
            view('livewire.modals.admin.groups.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un groupe',
                'availableUsers' => $this->availableUsers(),
                'availableApps' => $this->availableApps(),
                'roles' => Roles::all(),
            ]);
    }
}
