<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Chartnode;

/**
 * Composant Livewire pour la gestion des nœuds de l'organigramme (Chartnodes) dans l'interface d'administration.
 */
class ChartnodesManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'chartnodes';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'chartnodes';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les nœuds de l'organigramme.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => TRUE,
        'search' => ''
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
     * Rendu du composant.
     * Récupère les nœuds de l'organigramme filtrés et paginés.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'chartnodes' => Chartnode::filter($this->filter)
                ->orderBy('name', 'ASC')
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
