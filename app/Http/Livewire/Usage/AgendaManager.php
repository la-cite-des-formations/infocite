<?php

namespace App\Http\Livewire\Usage;

use App\Models\Event;
use Livewire\Component;

/**
 * Composant Livewire pour l'affichage de la rubrique Agenda.
 * Présente une timeline des événements futurs liés aux articles publiés,
 * ainsi qu'un calendrier mensuel FullCalendar (vue alternative).
 */
class AgendaManager extends Component
{
    /**
     * rubrique de l'agenda
     */
    public $agendaRubric;

    /**
     * Récupère les paramètres passés au composant lors du Livewire::mount().
     *
     * @param object $viewBag
     * @return void
     */
    public function mount($viewBag)
    {
        $this->agendaRubric = $viewBag->rubric;
    }

    /**
     * Construit la requête de base pour récupérer les événements publiés.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseEventsQuery()
    {
        return Event::whereHas('post', function ($query) {
            $query->where('published', true)
                ->where(function ($q) {
                    $q->whereNull('published_at')
                      ->orWhere('published_at', '<=', today()->format('Y-m-d'));
                })
                ->where(function ($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>', today()->format('Y-m-d'));
                });
        })
        ->with(['post', 'eventType']);
    }

    /**
     * Retourne les événements au format JSON pour FullCalendar (appelé via fetch JS).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents()
    {
        $events = $this->baseEventsQuery()
            ->orderBy('start_date', 'asc')
            ->get();

        $calendarEvents = $events->map(function ($event) {
            $start = $event->start_date->format('Y-m-d');
            if ($event->start_time) {
                $start .= 'T' . substr($event->start_time, 0, 5);
            }

            $end = null;
            if ($event->end_date) {
                $end = $event->end_date->format('Y-m-d');
                if ($event->end_time) {
                    $end .= 'T' . substr($event->end_time, 0, 5);
                } else {
                    // FullCalendar traite les dates de fin comme exclusives pour les all-day events
                    // Ajouter un jour pour que l'événement couvre bien le dernier jour
                    if (!$event->start_time) {
                        $end = $event->end_date->addDay()->format('Y-m-d');
                    }
                }
            }

            return [
                'title' => $event->post->title,
                'start' => $start,
                'end' => $end,
                'color' => $event->eventType->color ?? '#3498db',
                'url' => $event->post->route,
                'extendedProps' => [
                    'location' => $event->location,
                    'type' => $event->eventType->name ?? '',
                ],
            ];
        });

        return $calendarEvents;
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $events = $this->baseEventsQuery()
            ->with(['post.gallery'])
            ->where('start_date', '>=', today()->format('Y-m-d'))
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $calendarEvents = $this->getCalendarEvents();

        return view('livewire.usage.agenda-manager', [
            'events' => $events,
            'calendarEventsJson' => $calendarEvents->toJson(),
        ]);
    }
}
