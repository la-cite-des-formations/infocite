<?php

namespace App\Http\Livewire\Modals\Admin\Profiles;

use App\Models\App;
use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;

/**
 * Composant Livewire pour la modale d'édition d'un profil.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle du profil (utilisateur de type profil).
     *
     * @var \App\Models\User
     */
    public $profile;

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
    public $canAdd = TRUE;

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
     * Liste des identifiants des groupes associés au profil.
     *
     * @var \Illuminate\Support\Collection
     */
    public $groupsIDs;

    /**
     * Groupes liés sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedLinkedGroups = [];

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
     * Liste des identifiants des applications associées au profil.
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
     * Mot-clé de recherche pour les utilisateurs.
     *
     * @var string
     */
    public $userSearch = '';

    /**
     * Liste des identifiants des utilisateurs associés au profil.
     *
     * @var \Illuminate\Support\Collection
     */
    public $usersIDs;

    /**
     * Utilisateurs liés sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedLinkedUsers = [];

    /**
     * Utilisateurs disponibles sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedAvailableUsers = [];

    /**
     * Fonction à attribuer aux membres (via l'onglet fonction).
     *
     * @var string
     */
    public $function = '';

    /**
     * Configuration des onglets du formulaire principal.
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
     * Règles de validation pour le profil.
     *
     * @var array
     */
    protected $rules = [
        'profile.name' => 'required|string|max:255',
        'profile.first_name' => 'required|string|max:255',
    ];

    /**
     * Définit le profil et initialise les onglets du formulaire.
     *
     * @param int|null $id Identifiant de l'utilisateur de type profil.
     */
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

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID du profil et éventuellement le mode.
     */
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

    /**
     * Réinitialise les modifications en rechargeant les données depuis la base.
     */
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

    /**
     * Définit l'onglet courant et réinitialise les sélections.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];

        $this->updatedSelectedLinkedGroups();
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->profile = NULL;
        if ($mode !== 'view') $this->setProfile();

        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];

        $this->updatedSelectedLinkedGroups();
    }

    /**
     * Ajoute des éléments (utilisateurs, applications, groupes, fonction) selon l'onglet actif.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'users' : $this->addSelectedAvailableUsersAndApllyProfile();
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
     * Applique la configuration du profil (groupes et applications) aux utilisateurs liés sélectionnés.
     */
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

    /**
     * Associe les utilisateurs sélectionnés et leur applique la configuration du profil.
     */
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

    /**
     * Associe les applications sélectionnées au profil.
     */
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

    /**
     * Associe des groupes disponibles au profil.
     */
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

    /**
     * Retire des éléments (utilisateurs, applications, groupes, fonction) selon l'onglet actif.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'users' : $this->removeSelectedLinkedUsers();
            return;

            case 'apps' : $this->removeSelectedLinkedApps();
            return;

            case 'groups' : $this->removeSelectedLinkedGroups();
            return;

            case 'function' : $this->removeFunction();
            return;
        }
    }

    /**
     * Retire les utilisateurs sélectionnés du profil.
     */
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

    /**
     * Retire les applications sélectionnées du profil.
     */
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

    /**
     * Retire les groupes sélectionnés du profil.
     */
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

    /**
     * Retire la fonction attribuée aux groupes sélectionnés.
     */
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

    /**
     * Enregistre le profil ou les modifications.
     */
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
            ->whereNotIn('id', $this->profile->groups->pluck('id'))
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
            ->whereNotIn('id', $this->profile->apps->pluck('id'))
            ->orderByRaw('name ASC')
            ->get();
    }

    /**
     * Récupère la liste des utilisateurs disponibles pour l'association.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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
            view('livewire.modals.admin.profiles.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un profil',
                'availableGroups' => $this->availableGroups(),
                'availableApps' => $this->availableApps(),
                'availableUsers' => $this->availableUsers(),
            ]);
    }
}
