<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Right;

/**
 * Composant Livewire pour la gestion des droits d'accès dans l'interface d'administration.
 */
class RightsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'rights';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'rights';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les droits.
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
     *
     * @return \Illuminate\View\View
     */
    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'rights' => Right::filter($this->filter)
                ->paginate($this->perPage),
        ]);
    }
}
