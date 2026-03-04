<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Group;
use App\Models\User;

/**
 * Composant Livewire pour la gestion des utilisateurs dans l'interface d'administration.
 */
class UsersManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'users';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'users';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Informations contextuelles de l'utilisateur.
     *
     * @var mixed
     */
    public $userInfo;

    /**
     * Filtres de groupes disponibles.
     *
     * @var array
     */
    public $groupFilter;
    public $filter = [
        'searchOnly' => FALSE,
        'profiles' => FALSE,
        'search' => '',
        'groupType' => '',
        'groupId' => '',
        'isFrozen' => 0,
    ];

    /**
     * Options pour le nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [10, 15, 25];

    /**
     * Nombre d'éléments par page sélectionné.
     *
     * @var int
     */
    public $perPage = 10;

    /**
     * Initialisation du composant.
     * Charge les infos utilisateur et initialise les filtres de groupes.
     */
    public function mount() {
        $this->updatedFilter();
        $this->groupFilter = AP::getGroupFilter();
    }

    /**
     * Réinitialise l'identifiant du groupe lors du changement de type de groupe.
     */
    public function updatingFilterGroupType() {
        $this->filter['groupId'] = '';
    }

    /**
     * Met à jour la liste des groupes disponibles lors du changement de type de groupe.
     */
    public function updatedFilterGroupType() {
        $this->groupFilter = AP::getGroupFilter($this->filter['groupType']);
    }

    /**
     * Met à jour les informations contextuelles de l'utilisateur suite à un changement de filtre.
     */
    public function updatedFilter() {
        $this->userInfo = AP::getUserInfoParams($this->filter);
    }

    /**
     * Rendu du composant.
     * Récupère les groupes de filtrage et pagine les utilisateurs actifs.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $groupType = $this->filter['groupType'];

        return view('livewire.admin.models-manager', [
            'groups' => Group::query()
                ->when($groupType, function ($query) use ($groupType) {
                    $query->where('type', $groupType);
                })
                ->orderByRaw('name ASC')
                ->get(),
            'users' => User::filter($this->filter)
                ->orderByRaw('name ASC, first_name ASC')
                ->paginate($this->perPage),
        ]);
    }
}
