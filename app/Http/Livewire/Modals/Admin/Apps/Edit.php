<?php

namespace App\Http\Livewire\Modals\Admin\Apps;

use App\Models\App;
use App\Models\Group;
use App\Models\User;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'édition d'une application.
 */
class Edit extends Component
{
    use WithAlert;
    use WithIconpicker;

    /**
     * Modèle de l'application.
     *
     * @var \App\Models\App
     */
    public $app;

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
     * Type de groupe pour le filtrage ('C', 'P', etc.).
     *
     * @var string
     */
    public $groupType = 'C';

    /**
     * Liste des identifiants des groupes associés.
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
     * Mot-clé de recherche pour les groupes.
     *
     * @var string
     */
    public $groupSearch = '';

    /**
     * Liste des identifiants des utilisateurs associés.
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
     * Mot-clé de recherche pour les utilisateurs.
     *
     * @var string
     */
    public $userSearch = '';

    /**
     * Liste des identifiants des profils associés.
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
     * Mot-clé de recherche pour les profils.
     *
     * @var string
     */
    public $profileSearch = '';

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['render'];

    /**
     * Règles de validation pour l'application.
     *
     * @var array
     */
    protected $rules = [
        'app.owner_id' => 'nullable|integer',
        'app.auth_type' => 'required',
        'app.name' => 'required|string|max:255',
        'app.description' => 'nullable|string',
        'app.icon' => 'nullable|string|max:20',
        'app.url' => 'required|url|max:255',
    ];

    /**
     * Initialise l'application et la configuration des onglets.
     *
     * @param int|null $id Identifiant de l'application (optionnel).
     */
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

    /**
     * Récupère les identifiants des groupes, profils et utilisateurs associés.
     */
    private function setIDs() {
        $this->groupsIDs = $this->app->groups->pluck('id');
        $this->profilesIDs = $this->app->profiles->pluck('id');
        $this->usersIDs = $this->app->realUsers->pluck('id');
    }

    /**
     * Réinitialise les tableaux de sélection des éléments liés ou disponibles.
     */
    private function resetSelections() {
        $this->selectedLinkedGroups = [];
        $this->selectedAvailableGroups = [];
        $this->selectedLinkedProfiles = [];
        $this->selectedAvailableProfiles = [];
        $this->selectedLinkedUsers = [];
        $this->selectedAvailableUsers = [];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de l'application et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $authUser = auth()->user();

        $this->canAdd = $authUser->can('create', App::class) || $authUser->can('createFor', App::class);

        if ($authUser->can('createFor', App::class) && $authUser->cant('create', App::class)) {
            $this->rules['app.owner_id'] = 'required|integer';
        }

        $this->mode = $mode ?? 'view';
        $this->setApp($id ?? NULL);
        if (!empty($id)) {
            $this->setIDs();
        }
    }

    /**
     * Réinitialise les modifications en rechargeant les données depuis la base.
     */
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

    /**
     * Définit l'onglet courant et réinitialise les sélections.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        $this->resetSelections();
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->app = NULL;
        if ($mode !== 'view') $this->setApp();

        $this->resetSelections();
    }

    /**
     * Ajoute des éléments (groupes, profils ou utilisateurs) selon l'onglet actif.
     */
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

    /**
     * Associe les groupes sélectionnés à l'application.
     */
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

    /**
     * Associe les utilisateurs (réels ou profils) sélectionnés à l'application.
     *
     * @param string $usersType Type d'utilisateur ('real' ou 'profile').
     */
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

    /**
     * Retire des éléments (groupes, profils ou utilisateurs) selon l'onglet actif.
     */
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

    /**
     * Retire les groupes sélectionnés de l'application.
     */
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

    /**
     * Retire les utilisateurs (réels ou profils) sélectionnés de l'application.
     *
     * @param string $usersType Type d'utilisateur ('real' ou 'profile').
     */
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

    /**
     * Enregistre l'application ou les modifications.
     */
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

    /**
     * Récupère la liste des groupes disponibles pour l'association.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Récupère la liste des profils disponibles pour l'association.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Récupère la liste des utilisateurs réels disponibles pour l'association.
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
            ->whereNotIn('id', $this->app->realUsers->pluck('id'))
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
            view('livewire.modals.admin.apps.sheet') :
            view('livewire.modals.admin.models-form', [
                'modalSize' => 'modal-lg',
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
