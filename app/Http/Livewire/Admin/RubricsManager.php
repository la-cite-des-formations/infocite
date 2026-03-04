<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Rubric;

/**
 * Composant Livewire pour la gestion des rubriques dans l'interface d'administration.
 */
class RubricsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'rubrics';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'rubrics';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les rubriques.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => TRUE,
        'search' => '',
        //'is_parent' => NULL,
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
     * Récupère et pagine les rubriques filtrées.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'rubrics' => Rubric::filter($this->filter)
                ->orderByRaw('position ASC, rank ASC')
                ->paginate($this->perPage),
        ]);
    }
}
