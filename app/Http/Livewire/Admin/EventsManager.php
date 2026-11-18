<?php

namespace App\Http\Livewire\Admin;

use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Event;
use App\Models\EventType;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Composant Livewire pour la gestion des événements dans l'interface d'administration.
 */
class EventsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'events';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'events';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les événements.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => false,
        'search' => '',
        'event_type_id' => '',
        'start_date' => '',
        'end_date' => '',
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
            'dashboard' => 'events',
            'events' => Event::filter($this->filter)
                ->with(['eventType', 'post'])
                ->orderBy('start_date', 'desc')
                ->paginate($this->perPage),
            'eventTypes' => EventType::orderBy('name', 'asc')->get(),
        ]);
    }
}
