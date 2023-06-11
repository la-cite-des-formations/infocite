<?php

namespace App\Http\Livewire\Modals\Admin\Rights;

use App\Right;
use App\Roles;
use App\Http\Livewire\WithAlert;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $mode;
    public $canAdd = TRUE;

    public $right;
    public $defaultRolesCbx;
    public $dashboardRolesCbx;

    public $rightables;

    public $selectedAttachedGroups = [];
    public $selectedToAttachGroups = [];
    public $groupsRolesCbx;

    public $selectedAttachedUsers = [];
    public $selectedToAttachUsers = [];
    public $usersRolesCbx;

    public $priority;

    public $hasResource = FALSE;
    public $resourceType;
    public $resourceId;

    public $formTabs;
    public $groupsTabs;
    public $usersTabs;

    protected $listeners = ['render'];

    protected $rules = [
        'right.name' => 'required|string|max:255',
        'right.description' => 'required|string',
        'right.rd_role' => 'nullable|string|max:255',
        'right.rd_description' => 'required|string',
        'right.ed_role' => 'nullable|string|max:255',
        'right.ed_description' => 'required|string',
        'right.md_role' => 'nullable|string|max:255',
        'right.md_description' => 'required|string',
        'right.ad_role' => 'nullable|string|max:255',
        'right.ad_description' => 'required|string',
    ];

    public function setRight($id = NULL) {
        $this->right = $this->right ?? Right::findOrNew($id);

        foreach(Roles::all()->collection as $role) {
            $this->defaultRolesCbx[$role->id] = $this->right->default_roles & $role->flag;
            $this->dashboardRolesCbx[$role->id] = $this->right->dashboard_roles & $role->flag;
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.rights',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir les droits utilisateurs",
                    'hidden' => FALSE,
                ],
                'groups' => [
                    'icon' => 'groups',
                    'title' => "Gérer les groupes associés",
                    'hidden' => empty($this->right->id),
                ],
                'users' => [
                    'icon' => 'person',
                    'title' => "Gérer les utilisateurs associés",
                    'hidden' => empty($this->right->id),
                ],
            ],
        ];

        $this->groupsTabs = [
            'name' => 'groupsTabs',
            'currentTab' => 'groups',
            'panesPath' => 'includes.admin.rights',
            'withMarge' => FALSE,
            'tabs' => [
                'groups' => [
                    'icon' => 'group_add',
                    'title' => "Sélectionner des groupes à associer",
                    'hidden' => FALSE,
                ],
                'roles' => [
                    'icon' => 'theater_comedy',
                    'title' => "Choisir des rôles à attribuer",
                    'hidden' => FALSE,
                ],
            ],
        ];

        $this->usersTabs = [
            'name' => 'usersTabs',
            'currentTab' => 'users',
            'panesPath' => 'includes.admin.rights',
            'withMarge' => FALSE,
            'tabs' => [
                'users' => [
                    'icon' => 'group_add',
                    'title' => "Sélectionner des utilisateurs à associer",
                    'hidden' => FALSE,
                ],
                'roles' => [
                    'icon' => 'theater_comedy',
                    'title' => "Choisir des rôles à attribuer",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setRight($id ?? NULL);
    }

    public function refresh() {
        $this->right = Right::find($this->right->id);

        $this->selectedAttachedGroups = [];
        $this->selectedToAttachGroups = [];

        $this->selectedAttachedUsers = [];
        $this->selectedToAttachUsers = [];

        foreach(Roles::all()->collection as $role) {
            $this->defaultRolesCbx[$role->id] = $this->right->default_roles & $role->flag;
            $this->dashboardRolesCbx[$role->id] = $this->right->dashboard_roles & $role->flag;
        }

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

        if ($tabsSystem == 'formTabs') {
            switch ($tab) {
                case 'groups' :
                    $this->rightables = [
                        'models' => 'groups',
                        'class' => '\\App\\Group',
                        'orderByClause' => 'type ASC, name ASC',
                        'rolesCheckboxes' => 'groupsRolesCbx',
                        'attachedSelection' => 'selectedAttachedGroups',
                        'toAttachSelection' => 'selectedToAttachGroups',
                        'emptyToAttachSelectionMessage' => "Aucun groupes à attacher...",
                        'emptyAttachedSelectionMessage' => "Aucun groupes attachés sélectionnés...",
                    ];
                    $this->selectedToAttachGroups = [];
                break;

                case 'users' :
                    $this->rightables = [
                        'models' => 'users',
                        'class' => '\\App\\User',
                        'orderByClause' => 'name ASC, first_name ASC',
                        'rolesCheckboxes' => 'usersRolesCbx',
                        'attachedSelection' => 'selectedAttachedUsers',
                        'toAttachSelection' => 'selectedToAttachUsers',
                        'emptyToAttachSelectionMessage' => "Aucun utilisateurs à attacher...",
                        'emptyAttachedSelectionMessage' => "Aucun utilisateurs attachés sélectionnés...",
                    ];
                    $this->selectedToAttachUsers = [];
                break;
            }
        }
        $this->updatedSelectedAttachedRightables();
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->right = NULL;
        if ($mode !== 'view') $this->setRight();

        $this->selectedAttachedGroups = [];
        $this->selectedToAttachGroups = [];

        $this->selectedAttachedUsers = [];
        $this->selectedToAttachUsers = [];
    }

    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case  'groups' :
            case  'users' :
                $this->addRightables();
            return;

            case 'roles' :
                $this->addRoles();
            return;
        }
    }

    private function getSelectedRightablesData($attachedSelection) {
        return new Collection(array_map(
            function ($data) {
                $data = explode('|', $data);
                if (count($data) == 1) {
                    $data[1] = $data[2] = NULL;
                }
                return (object) array_combine(['rightable_id', 'resource_type', 'resource_id'], $data);
            },
            $this->{$attachedSelection}
        ));
    }

    private function getSelectedAttachedRightables() {
        $rightables = (object) $this->rightables;
        $selectedRightablesData = $this->getSelectedRightablesData($rightables->attachedSelection);

        return $this->right->{$rightables->models}
            ->filter(function ($rightable) use ($selectedRightablesData) {
                foreach ($selectedRightablesData as $data) {
                    if ($rightable->id == $data->rightable_id &&
                        $rightable->pivot->resource_type == $data->resource_type &&
                        $rightable->pivot->resource_id == $data->resource_id) return TRUE;
                }
                return FALSE;
            });
    }

    private function getUnselectedAttachedRightables() {
        $rightables = (object) $this->rightables;
        $selectedRightablesData = $this->getSelectedRightablesData($rightables->attachedSelection);

        return $this->right->{$rightables->models}
            ->filter(function ($rightable) use ($selectedRightablesData) {
                foreach ($selectedRightablesData as $data) {
                    if ($rightable->id == $data->rightable_id &&
                        $rightable->pivot->resource_type == $data->resource_type &&
                        $rightable->pivot->resource_id == $data->resource_id) return FALSE;
                }
                return TRUE;
            });
    }

    private function addRightables() {
        $rightables = (object) $this->rightables;

        if ($this->isEmpty($rightables->toAttachSelection, $rightables->emptyToAttachSelectionMessage)) return;

        $pivotData['roles'] = $this->right->default_roles;

        if (!is_null($this->priority)) {
            $pivotData['priority'] = $this->priority;
        }

        if ($this->hasResource) {
            if (!empty($this->resourceType)) {
                $pivotData['resource_type'] = $this->resourceType;

                if (is_numeric($this->resourceId)) {
                    $pivotData['resource_id'] = $this->resourceId;
                }
                else {
                    $errorMessage = "Veuillez choisir une resource, puis recommencez SVP.";
                }
            }
            else {
                $errorMessage = "Veuillez choisir un type de ressource et une ressource, puis recommencez SVP.";
            }
        }
        else {
            $pivotData['resource_type'] = NULL;
            $pivotData['resource_id'] = NULL;
        }

        if (!isset($errorMessage)) {
            $rightablesIds = [];

            foreach ($this->{$rightables->toAttachSelection} as $rightableId) {
                $isNewPivotData = TRUE;
                foreach ($this->right->{$rightables->models} as $rightable) {
                    if ($rightable->id == $rightableId &&
                        $rightable->pivot->resource_type == $pivotData['resource_type'] &&
                        $rightable->pivot->resource_id == $pivotData['resource_id']) {

                        $isNewPivotData = FALSE;
                        break;
                    }
                }
                if ($isNewPivotData) $rightablesIds[] = $rightableId;
            }

            $this->right
                ->{$rightables->models}()
                ->attach(array_fill_keys($rightablesIds, $pivotData));

            $this->{$rightables->toAttachSelection} = [];
            $this->{$rightables->attachedSelection} = [];
            $this->updatedSelectedAttachedRightables();

            $renderBag = [
                'alertClass' => 'success',
                'message' => "Importation effectuée avec succès."
            ];
        }
        else {
            $renderBag = [
                'alertClass' => 'warning',
                'message' => $errorMessage
            ];
        }
        $this->emit('render', $renderBag)->self();
    }

    private function updateRightables() {
        $rightables = (object) $this->rightables;

        if ($this->isEmpty($rightables->attachedSelection, $rightables->emptyAttachedSelectionMessage)) return;

        $pivotData = [];

        if (is_numeric($this->priority)) {
            $pivotData['priority'] = $this->priority;
        }

        if ($this->hasResource && !empty($this->resourceType)) {
            $pivotData['resource_type'] = $this->resourceType;

            if (is_numeric($this->resourceId)) {
                $pivotData['resource_id'] = $this->resourceId;
            }
        }
        elseif ($this->hasResource === FALSE) {
            $pivotData['resource_type'] = NULL;
            $pivotData['resource_id'] = NULL;
        }

        $selectedValidRightables = [];

        foreach($this->getSelectedAttachedRightables() as $key => $rightable) {
            $isValidUpdatingPivotData = TRUE;
            foreach ($this->getUnselectedAttachedRightables() as $unselectedRightable) {
                if ($unselectedRightable->id == $rightable->id &&
                    !empty($pivotData['resource_type']) && isset($pivotData['resource_id']) && is_numeric($pivotData['resource_id']) &&
                    $unselectedRightable->pivot->resource_type == $pivotData['resource_type'] &&
                    $unselectedRightable->pivot->resource_id == $pivotData['resource_id']) {

                    $isValidUpdatingPivotData = FALSE;
                    break;
                }
            }
            if ($isValidUpdatingPivotData) {
                foreach ($this->getSelectedAttachedRightables() as $key2 => $rightable2) {
                    if ($key !== $key2 && $rightable2->id == $rightable->id && $this->hasResource !== NULL) {
                        $isValidUpdatingPivotData = FALSE;
                        break;
                    }
                }
                if ($isValidUpdatingPivotData) $selectedValidRightables[] = $rightable;
            }
        }

        foreach($selectedValidRightables as $rightable) {
            $this->right
                ->{$rightables->models}()
                ->newPivotQuery()
                ->where('rightable_type', $rightable->getMorphClass())
                ->where('rightable_id', $rightable->id)
                ->where('resource_type', $rightable->pivot->resource_type)
                ->where('resource_id', $rightable->pivot->resource_id)
                ->when(!empty($pivotData), function ($query) use($pivotData) {
                    $query->update($pivotData);
                });
        }

        $this->{$rightables->attachedSelection} = [];
        $this->updatedSelectedAttachedRightables();
    }

    private function addRoles() {
        $rightables = (object) $this->rightables;
        if ($this->isEmpty($rightables->attachedSelection, $rightables->emptyAttachedSelectionMessage)) return;

        $unassignedRolesFlag = Roles::ALL;
        $assignedRolesFlag = Roles::NONE;

        foreach (Roles::all()->collection as $role) {
            if ($this->{$rightables->rolesCheckboxes}[$role->id] === FALSE) $unassignedRolesFlag ^= $role->flag;
            if ($this->{$rightables->rolesCheckboxes}[$role->id] === TRUE) $assignedRolesFlag |= $role->flag;
        }

        foreach($this->getSelectedAttachedRightables() as $rightable) {
            $this->right
                ->{$rightables->models}()
                ->newPivotQuery()
                ->where('rightable_type', $rightable->getMorphClass())
                ->where('rightable_id', $rightable->id)
                ->where('resource_type', $rightable->pivot->resource_type)
                ->where('resource_id', $rightable->pivot->resource_id)
                ->update(['roles' => $rightable->pivot->roles & $unassignedRolesFlag | $assignedRolesFlag]);
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
            case 'groups' :
            case 'users' :
                $this->removeRightables();
            return;

            case 'roles' :
                $this->removeRoles();
            return;
        }
    }

    private function removeRightables() {
        $rightables = (object) $this->rightables;
        if ($this->isEmpty($rightables->attachedSelection, $rightables->emptyAttachedSelectionMessage)) return;

        foreach($this->getSelectedAttachedRightables() as $rightable) {
            $this->right
                ->{$rightables->models}()
                ->newPivotQuery()
                ->where('rightable_type', $rightable->getMorphClass())
                ->where('rightable_id', $rightable->id)
                ->where('resource_type', $rightable->pivot->resource_type)
                ->where('resource_id', $rightable->pivot->resource_id)
                ->delete();
        }

        $this->{$rightables->attachedSelection} = [];
        $this->updatedSelectedAttachedRightables();

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Retrait effectué avec succès."
            ])
            ->self();
    }

    private function removeRoles() {
        $rightables = (object) $this->rightables;
        if ($this->isEmpty($rightables->attachedSelection, $rightables->emptyAttachedSelectionMessage)) return;

        foreach($this->getSelectedAttachedRightables() as $rightable) {
            $this->right
                ->{$rightables->models}()
                ->newPivotQuery()
                ->where('rightable_type', $rightable->getMorphClass())
                ->where('rightable_id', $rightable->id)
                ->where('resource_type', $rightable->pivot->resource_type)
                ->where('resource_id', $rightable->pivot->resource_id)
                ->update(['roles' => Roles::NONE]);
        }

        $this->{$rightables->attachedSelection} = [];

        $this->updatedSelectedAttachedRightables();

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Rôles retirés avec succès."
            ])
            ->self();
    }

    public function save() {
        if ($this->mode === 'view') return;

        $this->right->default_roles =
        $this->right->dashboard_roles = Roles::NONE;

        foreach(Roles::all()->collection as $role) {
            $this->right->default_roles |= $this->defaultRolesCbx[$role->id] ? $role->flag : Roles::NONE;
            $this->right->dashboard_roles |= $this->dashboardRolesCbx[$role->id] ? $role->flag : Roles::NONE;
        }

        $this->validate();
        $this->right->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Création des droits utilisateurs effectuée avec succès."
                ])
                ->self();
        }
        else {
            $rightablesTabs = $this->{$this->rightables['models'].'Tabs'};

            if ($this->formTabs['currentTab'] != 'general' && $rightablesTabs['currentTab'] == $this->rightables['models']) {
                $this->updateRightables();
            }

            $this
                ->emit('render', [
                    'alertClass' => 'success',
                    'message' => "Modification effectuée avec succès."
                ])
                ->self();
        }
    }

    private function updatedSelectedAttachedRightables() {
        if (empty($this->rightables)) return;

        $rightables = (object) $this->rightables;

        if ($this->{"{$rightables->models}Tabs"}['currentTab'] === $rightables->models) {
            if (!empty($this->{$rightables->attachedSelection})) {

                $selectedAttachedRightables = $this->getSelectedAttachedRightables();
                $firstRightablePivot = $selectedAttachedRightables->first()->pivot;

                $this->priority = $firstRightablePivot->priority;
                $this->hasResource = !empty($firstRightablePivot->resource_type) && is_numeric($firstRightablePivot->resource_id);
                $this->resourceType = $firstRightablePivot->resource_type;
                $this->resourceId = $firstRightablePivot->resource_id;

                foreach($selectedAttachedRightables as $rightable) {
                    if ($this->priority !== $rightable->pivot->priority) {
                        $this->priority = NULL;
                    }

                    if ($this->hasResource !== (!empty($rightable->pivot->resource_type) && is_numeric($rightable->pivot->resource_id))) {
                        $this->hasResource = NULL;

                    }

                    if ($this->resourceType !== $rightable->pivot->resource_type) {
                        $this->resourceType = NULL;
                    }
                    elseif ($this->resourceId !== $rightable->pivot->resource_id) {
                        $this->resourceId = NULL;
                    }
                }
            }
            else {
                $this->priority = NULL;
                $this->hasResource = FALSE;
                $this->resourceType = NULL;
                $this->resourceId = NULL;
            }
            $this->emit('setIndeterminateCbx', $rightables->models, ['resource' => $this->hasResource]);
        }

        if ($this->{"{$rightables->models}Tabs"}['currentTab'] === 'roles') {
            if (!empty($this->{$rightables->attachedSelection})) {

                $rolesFlags = $this->getSelectedAttachedRightables()->pluck('pivot')->pluck('roles');

                foreach (Roles::all()->collection as $role) {
                    // strictement coché  => TRUE ; strictement décoché => FALSE
                    $this->{$rightables->rolesCheckboxes}[$role->id] = $rolesFlags[0] & $role->flag ? TRUE : FALSE;

                    for ($i = 1; $i < count($rolesFlags) && !(($rolesFlags[$i] ^ $rolesFlags[$i-1]) & $role->flag); $i++);

                    // indéterminé => NULL
                    if ($i < count($rolesFlags)) $this->{$rightables->rolesCheckboxes}[$role->id] = NULL;
                }
            }
            else $this->{$rightables->rolesCheckboxes} = array_fill_keys(Roles::all()->collection->pluck('id')->toArray(), FALSE);

            $this->emit('setIndeterminateCbx', $rightables->models, $this->{$rightables->rolesCheckboxes});
        }
    }

    public function updatedSelectedAttachedGroups() {
        $this->selectedToAttachGroups = [];
        $this->updatedSelectedAttachedRightables();
    }

    public function updatedSelectedAttachedUsers() {
        $this->selectedToAttachUsers = [];
        $this->updatedSelectedAttachedRightables();
    }

    private function updatedSelectedToAttachRightables() {
        $this->{$this->rightables['attachedSelection']} = [];
        $this->updatedSelectedAttachedRightables();
    }

    public function updatedSelectedToAttachGroups() {
        $this->updatedSelectedToAttachRightables();
    }

    public function updatedSelectedToAttachUsers() {
        $this->updatedSelectedToAttachRightables();
    }

    public function updatedResourceType() {
        $this->resourceId = NULL;
    }

    private function getAttachableRightables() {
        if (empty($this->rightables)) return [];

        $rightables = (object)$this->rightables;

        return $rightables->class::query()
                ->orderByRaw($rightables->orderByClause)
                ->get();
    }

    private function getResourceables() {
        if (!empty($this->resourceType)) {
            $model = "\\App\\{$this->resourceType}";

            return $model::sort();
        }
        return NULL;
    }

    public function render($messageBag = NULL)
    {
        if ($messageBag) {
            extract($messageBag);
            session()->flash('alertClass', $alertClass);
            session()->flash('message', $message);
        }

        $viewBag = [
            'addButtonTitle' => 'Ajouter des droits utilisateurs',
            'roles' => Roles::all()->collection,
        ];
        if ($this->mode === 'view') {
            $view = 'livewire.modals.admin.rights.sheet';
        }
        else {
            $view = 'livewire.modals.admin.models-form';
            $viewBag['modalSize'] = 'modal-lg';
            $viewBag['attachableRightables'] = $this->getAttachableRightables();
            $viewBag['resourceables'] = $this->getResourceables();
        }

        return view($view, $viewBag);
    }
}
