<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Models\Event;
use Livewire\Component;

/**
 * Composant Livewire affichant le détail d'un événement dans une modale.
 * Utilisé notamment lors du clic sur un événement dans le calendrier FullCalendar
 * ou depuis la timeline de l'agenda.
 */
class EventDetail extends Component
{
    /**
     * Événement affiché dans la modale.
     *
     * @var \App\Models\Event|null
     */
    public $event;

    /**
     * Initialisation : charge l'événement depuis les données transmises par la modale.
     *
     * @param array $data Données contenant l'ID de l'événement ('eventId' ou 'id').
     */
    public function mount($data)
    {
        $id = $data['eventId'] ?? $data['id'] ?? null;
        if ($id) {
            $this->event = Event::with(['eventType', 'post'])->find($id);
        }
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.usage.event-detail');
    }
}
