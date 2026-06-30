<?php

namespace App\Http\Livewire\Usage;

use App\Models\Event;
use Livewire\Component;

/**
 * Composant Livewire pour l'affichage de la rubrique Agenda.
 * Présente une timeline des événements futurs liés aux articles publiés.
 */
class AgendaManager extends Component
{
    /**
     * Sac de données (ViewBag) passé par le ViewController.
     *
     * @var object
     */
    public $viewBag;

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $events = Event::whereHas('post', function ($query) {
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
        ->with(['post.gallery', 'eventType'])
        ->where('start_date', '>=', today()->format('Y-m-d'))
        ->orderBy('start_date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

        return view('livewire.usage.agenda-manager', [
            'events' => $events,
        ]);
    }
}
