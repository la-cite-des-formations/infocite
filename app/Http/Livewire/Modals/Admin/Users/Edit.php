<?php

namespace App\Http\Livewire\Modals\Admin\Users;

use App\Models\App;
use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;

/**
 * Composant Livewire pour la modale d'édition d'un utilisateur.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle de l'utilisateur.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Mode d'affichage ou d'édition.
     *
     * @var string
     */
    public $mode;

    /**
     * Indique si l'ajout est autorisé.
     *
     * @var bool
     */
    public $canAdd;

    /**
     * Type de groupe pour le filtrage ('C', 'P', etc.).
     *
     * @var string
     */
    public $groupType = 'C';

    /**
     * Mot-clé de recherche pour les groupes.
     *
     * @var string
     */
    public $groupSearch = '';

    /**
     * Liste des identifiants des groupes de l'utilisateur.
     *
     * @var \Illuminate\Support\Collection
     */
    public $groupsIDs;

    /**
     * Groupes de l'utilisateur sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedUserGroups = [];

    /**
     * Groupes disponibles sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedAvailableGroups = [];

    /**
     * Mot-clé de recherche pour les applications.
     *
     * @var string
     */
    public $appSearch = '';

    /**
     * Liste des identifiants des applications de l'utilisateur.
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
     * Liste des identifiants des profils associés à l'utilisateur.
     *
     * @var \Illuminate\Support\Collection
     */
    public $profilesIDs;

    /**
     * Profils sélectionnés dans l'interface (pour application).
     *
     * @var array
     */
    public $selectedProfiles = [];

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
     * Fonction à attribuer (onglet fonction).
     *
     * @var string
     */
    public $function = '';

    /**
     * Nombre minimum de classes à afficher avant troncature.
     *
     * @var int
     */
    public $classesMin = 5;

    /**
     * Nombre total de classes de l'utilisateur.
     *
     * @var int
     */
    public $userNbClasses;

    /**
     * Indique si la liste des classes doit être tronquée.
     *
     * @var bool
     */
    public $truncateClassesList;

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    /**
     * Configuration des sous-onglets pour la gestion des groupes.
     *
     * @var array
     */
    public $groupsTabs;

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['render'];

    /**
     * Règles de validation pour l'utilisateur.
     *
     * @var array
     */
    protected $rules = [
        'user.name' => 'required|string|max:255',
        'user.first_name' => 'required|string|max:255',
        'user.email' => 'nullable|email|max:255',
        'user.password' => 'nullable|string|min:8|max:255',
    ];

    /**
     * Définit l'utilisateur et initialise les onglets du formulaire.
     *
     * @param int|null $id Identifiant de l'utilisateur.
     */
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
                'profiles' => [
                    'icon' => 'portrait',
                    'title' => "Associer et appliquer les profils à l'utilisateur",
                    'hidden' => !$this->user->id,
                ],
                'apps' => [
                    'icon' => 'view_module',
                    'title' => "Gérer les applications de l'utilisateur",
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

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de l'utilisateur et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $this->canAdd = auth()->user()->can('create', User::class);
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

    /**
     * Réinitialise les modifications en rechargeant les données.
     */
    public function refresh() {
        $this->user->groups()->sync($this->groupsIDs);
        $this->selectedUserGroups = [];
        $this->selectedAvailableGroups = [];

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

    /**
     * Définit l'onglet courant et réinitialise les sélections.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        $this->selectedAvailableGroups = [];
        $this->selectedUserGroups = [];
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->user = NULL;
        if ($mode !== 'view') $this->setUser();

        $this->selectedAvailableGroups = [];
        $this->selectedUserGroups = [];
    }

    /**
     * Bascule l'affichage complet ou tronqué de la liste des classes.
     */
    public function switchClasses() {
        $this->truncateClassesList = !$this->truncateClassesList;
    }

    /**
     * Ajoute des éléments (profils, applications, groupes, fonction) selon l'onglet actif.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
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

    /**
     * Applique la configuration des profils sélectionnés (groupes et applications) à l'utilisateur.
     */
    public function applyProfiles() {
        $this->selectedProfiles = array_merge($this->selectedAvailableProfiles, $this->selectedLinkedProfiles);

        if ($this->isEmpty('selectedProfiles', "Aucun profil sélectionné")) return;

        $user = $this->user;

        $profiles = User::whereIn('id', $this->selectedProfiles)->get();

        foreach ($profiles as $profile) {
            $profileGroups = $profile->groups->pluck('function', 'id')
                ->map(function ($function) {
                    return ['function' => $function];
                });

            $profileApps = $profile->apps->pluck('id');

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

    /**
     * Associe les profils sélectionnés à l'utilisateur.
     */
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

    /**
     * Associe les applications sélectionnées à l'utilisateur.
     */
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

    /**
     * Associe des groupes disponibles à l'utilisateur.
     */
    /**
     * Associe les groupes sélectionnés à l'utilisateur.
     */
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

    /**
     * Attribue une fonction aux groupes sélectionnés pour l'utilisateur.
     */
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

    /**
     * Retire des éléments (profils, applications, groupes, fonction) selon l'onglet actif.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
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
        }
    }

    /**
     * Retire les profils sélectionnés de l'utilisateur.
     */
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

    /**
     * Retire les applications sélectionnées de l'utilisateur.
     */
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

    /**
     * Retire les groupes sélectionnés de l'utilisateur.
     */
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

    /**
     * Retire la fonction attribuée aux groupes sélectionnés.
     */
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

    /**
     * Enregistre l'utilisateur ou les modifications.
     */
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

    /**
     * Récupère la liste des groupes disponibles pour l'association.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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
            ->whereNotIn('id', $this->user->apps->pluck('id'))
            ->orderByRaw('name ASC')
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
            ->whereNotIn('id', $this->user->profiles->pluck('id'))
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
            $this->sendAlert($messageBag);
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
