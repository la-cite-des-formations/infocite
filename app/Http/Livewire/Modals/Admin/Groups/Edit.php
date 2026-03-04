<?php

namespace App\Http\Livewire\Modals\Admin\Groups;

use App\Models\App;
use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;

/**
 * Composant Livewire pour la modale d'édition d'un groupe.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle du groupe.
     *
     * @var \App\Models\Group
     */
    public $group;

    /**
     * Mode d'affichage ou d'édition.
     *
     * @var string
     */
    public $mode;

    /**
     * Indique si l'ajout est autorisé.
     *
     * @var bool|null
     */
    public $canAdd;

    /**
     * Fonction à attribuer aux membres (pour l'onglet fonction).
     *
     * @var string
     */
    public $function = '';

    /**
     * Liste des rôles sous forme de cases à cocher.
     *
     * @var array
     */
    public $rolesCheckboxes;

    /**
     * Mot-clé de recherche pour les utilisateurs.
     *
     * @var string
     */
    public $userSearch = '';

    /**
     * Liste des identifiants des membres du groupe.
     *
     * @var \Illuminate\Support\Collection
     */
    public $membersIDs;

    /**
     * Membres sélectionnés dans l'interface (pour action groupée).
     *
     * @var array
     */
    public $selectedMembers = [];

    /**
     * Utilisateurs disponibles sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedAvailableUsers = [];

    /**
     * Mot-clé de recherche pour les applications.
     *
     * @var string
     */
    public $appSearch = '';

    /**
     * Liste des identifiants des applications du groupe.
     *
     * @var \Illuminate\Support\Collection
     */
    public $appsIDs;

    /**
     * Applications liées sélectionnées dans l'interface.
     *
     * @var array
     */
    public $selectedLinkedApps = [];

    /**
     * Applications disponibles sélectionnées dans l'interface.
     *
     * @var array
     */
    public $selectedAvailableApps = [];

    /**
     * Mot-clé de recherche pour les profils.
     *
     * @var string
     */
    public $profileSearch = '';

    /**
     * Liste des identifiants des profils associés au groupe.
     *
     * @var \Illuminate\Support\Collection
     */
    public $profilesIDs;

    /**
     * Profils liés sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedLinkedProfiles = [];

    /**
     * Profils disponibles sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedAvailableProfiles = [];

    /**
     * Configuration des onglets du formulaire principal.
     *
     * @var array
     */
    public $formTabs;

    /**
     * Configuration des sous-onglets pour la gestion des membres.
     *
     * @var array
     */
    public $membersTabs;

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['render'];

    /**
     * Règles de validation pour le groupe.
     *
     * @var array
     */
    protected $rules = [
        'group.name' => 'required|string|max:255',
        'group.type' => 'required',
        'function' => 'string|max:255',
    ];

    /**
     * Définit le groupe et initialise les onglets du formulaire.
     *
     * @param int|null $id Identifiant du groupe.
     */
    public function setGroup($id = NULL) {
        $this->group = $this->group ?? Group::findOrNew($id);

        $authUser = auth()->user();

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' =>
                $authUser->can('create', Group::class) || $authUser->can('update', $this->group) ?
                    'general' :
                    'members',
            'panesPath' => 'includes.admin.groups',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le groupe",
                    'hidden' => $authUser->cant('create', Group::class) && $authUser->cant('update', $this->group),
                ],
                'members' => [
                    'icon' => 'groups',
                    'title' => "Gérer les membres du groupe",
                    'hidden' => !$this->group->id,
                ],
                'profiles' => [
                    'icon' => 'portrait',
                    'title' => "Gérer les profils associés au groupe",
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
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID du groupe et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $this->canAdd = auth()->user()->can('create', Group::class);
        $this->mode = $mode ?? 'view';
        $this->setGroup($id ?? NULL);

        if (!empty($id)) {
            $this->membersIDs = $this->group->users->pluck('id');
            $this->appsIDs = $this->group->apps->pluck('id');
        }
    }

    /**
     * Réinitialise les modifications en rechargeant les données depuis la base.
     */
    public function refresh() {
        $this->group->users()->sync($this->membersIDs);
        $this->selectedMembers = [];
        $this->selectedAvailableUsers = [];

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

    /**
     * Définit l'onglet courant et réinitialise les sélections.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        $this->selectedAvailableUsers = [];
        $this->selectedMembers = [];
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->group = NULL;
        if ($mode !== 'view') $this->setGroup();

        $this->selectedMembers = [];
        $this->selectedAvailableUsers = [];
    }

    /**
     * Ajoute des éléments (profils, applications, membres, fonction) selon l'onglet actif.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'profiles' : $this->addSelectedAvailableProfiles();
            return;

            case 'apps' : $this->addSelectedAvailableApps();
            return;

            case  'members' : $this->addSelectedAvailableUsers();
            return;

            case 'function' : $this->addFunction();
            return;
        }
    }

    /**
     * Associe les profils sélectionnés au groupe.
     */
    private function addSelectedAvailableProfiles() {
        if ($this->isEmpty('selectedAvailableProfiles', "Aucun profil sélectionné")) return;

        $this->group
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

    /**
     * Associe les applications sélectionnées au groupe.
     */
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

    /**
     * Associe les utilisateurs sélectionnés en tant que membres au groupe.
     */
    private function addSelectedAvailableUsers() {
        if ($this->isEmpty('selectedAvailableUsers', "Aucun utilisateur sélectionné")) return;

        $this->group
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

    /**
     * Attribue une fonction aux membres sélectionnés du groupe.
     */
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

    /**
     * Retire des éléments (profils, applications, membres, fonction) selon l'onglet actif.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'profiles' : $this->removeSelectedLinkedProfiles();
            return;

            case 'apps' : $this->removeSelectedLinkedApps();
            return;

            case 'members' : $this->removeSelectedMembers();
            return;

            case 'function' : $this->removeFunction();
            return;
        }
    }

    /**
     * Retire les profils sélectionnés du groupe.
     */
    private function removeSelectedLinkedProfiles() {
        if ($this->isEmpty('selectedLinkedProfiles', "Aucun profil sélectionné")) return;

        $this->group
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

    /**
     * Retire les applications sélectionnées du groupe.
     */
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

    /**
     * Retire les membres sélectionnés du groupe.
     */
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

    /**
     * Retire la fonction attribuée aux membres sélectionnés.
     */
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

    /**
     * Enregistre le groupe ou les modifications.
     */
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

    /**
     * Récupère la liste des applications disponibles pour l'association.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Récupère la liste des utilisateurs disponibles pour devenir membres.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function availableUsers() {
        $search = $this->userSearch;
        return User::query()
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', name) LIKE ?", "%{$search}%");
            })
            ->where('name', '<>', AP::PROFILE)
            ->when($this->group->type !== 'S', function ($users) {
                $users->where('is_frozen', FALSE);
            })
            ->when($this->group->type === 'C', function ($users) {
                $users->where('is_staff', FALSE);
            })
            ->when($this->group->type === 'P' || $this->group->type === 'E', function ($users) {
                $users->where('is_staff', TRUE);
            })
            ->when($this->group->type === 'F', function ($users) {
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

    /**
     * Récupère la liste des profils disponibles pour l'association.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function availableProfiles() {
        $search = $this->profileSearch;
        return User::query()
            ->when($search, function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%");
            })
            ->where('name', AP::PROFILE)
            ->whereNotIn('id', $this->group->profiles->pluck('id'))
                ->orderByRaw('first_name ASC')
                ->get();
    }

    /**
     * Rendu du composant.
     *
     * @param array|null $messageBag Sac de messages d'alerte (optionnel).
     * @return \Illuminate\View\View
     */
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
                'availableProfiles' => $this->availableProfiles(),
            ]);
    }
}
