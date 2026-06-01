<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Chartnode;
use App\Models\User;

/**
 * Composant Livewire pour la gestion des acteurs (utilisateurs attachés à l'organigramme) dans l'interface d'administration.
 */
class ActorsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré par le composant.
     *
     * @var string
     */
    public $models = 'actors';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'actors';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Informations sur l'acteur courant.
     *
     * @var mixed
     */
    public $actorInfo;

    /**
     * Filtres de recherche et d'affichage.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => FALSE,
        'profiles' => FALSE,
        'search' => '',
        'groupType' => 'P+',
        'groupId' => '',
        'showUndefinedLinks' => TRUE,
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
     * Active l'affichage des filtres et déclenche la première mise à jour.
     */
    public function mount() {
        $this->showFilter = TRUE;
        $this->updatedFilter();
    }

    /**
     * Mise à jour des paramètres d'information des acteurs lors du changement de filtre.
     */
    public function updatedFilter() {
        $this->actorInfo = AP::getUserInfoParams($this->filter);
    }

    /**
     * Rendu du composant.
     * Récupère la liste des nœuds de l'organigramme et les acteurs filtrés/paginés.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'chartnodes' => Chartnode::query()
                ->orderByRaw('name ASC')
                ->get(),
            'actors' => User::filter($this->filter)
                ->orderBy('name', 'ASC')
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
