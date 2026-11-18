<?php

namespace App\Http\Livewire\Usage;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Rubric;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

/**
 * Composant Livewire pour l'affichage de la rubrique Agenda.
 * Présente une timeline des événements futurs (autonomes ou liés aux articles publiés),
 * un calendrier mensuel FullCalendar (vue alternative), et un système de filtres multicritères.
 */
class AgendaManager extends Component
{
    use AuthorizesRequests;

    /**
     * Rubrique de l'agenda.
     */
    public $agendaRubric;

    /**
     * Filtres de sélection.
     */
    public $filterType = '';
    public $filterStartDate;
    public $filterEndDate;
    public $filterSearch = '';

    /**
     * Vue active : 'timeline' ou 'calendar'.
     */
    public $currentView = 'timeline';

    /**
     * Gestion du premier chargement pour éviter la réactivation intempestive d'AOS au filtrage.
     */
    public $firstLoad = TRUE;
    public $rendered = FALSE;

    protected $listeners = [
        'refreshAgenda' => '$refresh',
        'eventImported' => '$refresh',
        'deleteEvent' => 'deleteEvent',
    ];

    /**
     * Récupère les paramètres passés au composant lors du Livewire::mount().
     *
     * @param object $viewBag
     * @return void
     */
    public function mount($viewBag)
    {
        $this->agendaRubric = $viewBag->rubric ?? Rubric::where('segment', 'agenda')->first();
        $this->filterStartDate = today()->format('Y-m-d');
        $this->filterEndDate = today()->addDays(365)->format('Y-m-d');
    }

    /**
     * Bascule entre la vue Timeline et la vue Calendrier.
     *
     * @param string $view
     * @return void
     */
    public function switchView($view)
    {
        $this->currentView = in_array($view, ['timeline', 'calendar']) ? $view : 'timeline';
        $this->dispatchBrowserEvent('agenda-view-switched', ['view' => $this->currentView]);
    }

    /**
     * Réinitialise les filtres à leurs valeurs par défaut.
     */
    public function resetFilters()
    {
        $this->filterType = '';
        $this->filterStartDate = today()->format('Y-m-d');
        $this->filterEndDate = today()->addDays(365)->format('Y-m-d');
        $this->filterSearch = '';

        $this->dispatchBrowserEvent('agenda-events-updated', [
            'events' => $this->getCalendarEvents(),
        ]);
    }

    /**
     * Hook appelé lors de la mise à jour d'un filtre.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterType', 'filterStartDate', 'filterEndDate', 'filterSearch'])) {
            $this->dispatchBrowserEvent('agenda-events-updated', [
                'events' => $this->getCalendarEvents(),
            ]);
        }
    }

    /**
     * Supprime un événement directement depuis l'agenda.
     *
     * @param int $eventId
     */
    public function deleteEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $this->authorize('delete', $event);

        $event->delete();

        session()->flash('alertClass', 'success');
        session()->flash('message', "L'événement <strong>{$event->title}</strong> a été supprimé avec succès.");

        $this->dispatchBrowserEvent('agenda-events-updated', [
            'events' => $this->getCalendarEvents(),
        ]);
    }

    /**
     * Construit la requête de base pour récupérer les événements publiés ou autonomes.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseEventsQuery()
    {
        return Event::where(function ($query) {
            $query->whereNull('post_id')
                ->orWhereHas('post', function ($postQ) {
                    $postQ->where('published', true)
                        ->where(function ($q) {
                            $q->whereNull('published_at')
                              ->orWhere('published_at', '<=', today()->format('Y-m-d'));
                        })
                        ->where(function ($q) {
                            $q->whereNull('expired_at')
                              ->orWhere('expired_at', '>', today()->format('Y-m-d'));
                        });
                });
        })
        ->with(['post.gallery', 'eventType']);
    }

    /**
     * Construit la requête filtrée selon les critères choisis par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function filteredEventsQuery()
    {
        $query = $this->baseEventsQuery();

        $query->filter([
            'event_type_id' => $this->filterType,
            'start_date' => $this->filterStartDate,
            'end_date' => $this->filterEndDate,
            'search' => $this->filterSearch,
        ]);

        return $query;
    }

    /**
     * Retourne les événements au format JSON pour FullCalendar.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCalendarEvents()
    {
        $events = $this->filteredEventsQuery()
            ->orderBy('start_date', 'asc')
            ->get();

        return $events->map(function ($event) {
            $start = $event->start_date ? $event->start_date->format('Y-m-d') : null;
            if ($event->start_time) {
                $start .= 'T' . substr($event->start_time, 0, 5);
            }

            $end = null;
            if ($event->end_date) {
                $end = $event->end_date->format('Y-m-d');
                if ($event->end_time) {
                    $end .= 'T' . substr($event->end_time, 0, 5);
                } else {
                    if (!$event->start_time) {
                        $end = $event->end_date->addDay()->format('Y-m-d');
                    }
                }
            }

            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $start,
                'end' => $end,
                'color' => $event->eventType->color ?? '#3498db',
                'extendedProps' => [
                    'location' => $event->location,
                    'type' => $event->eventType->name ?? '',
                    'description' => \Illuminate\Support\Str::limit($event->description, 120),
                    'hasPost' => (bool) $event->post_id,
                ],
            ];
        });
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->firstLoad = !$this->rendered;
        $this->rendered = TRUE;

        $events = $this->filteredEventsQuery()
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $calendarEvents = $this->getCalendarEvents();

        return view('livewire.usage.agenda-manager', [
            'events' => $events,
            'eventTypes' => EventType::orderBy('name', 'asc')->get(),
            'calendarEventsJson' => $calendarEvents->toJson(),
        ]);
    }
}
