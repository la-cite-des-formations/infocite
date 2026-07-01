<?php

namespace App\Http\Livewire\Admin;

use App\Models\EventType;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;

/**
 * Composant Livewire pour la gestion des types d'événements dans l'interface d'administration.
 */
class EventTypesManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'event-types';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'eventTypes';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les types d'événements.
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
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'eventTypes' => EventType::filter($this->filter)
                ->orderByRaw('name ASC')
                ->paginate($this->perPage),
        ]);
    }
}
