<?php

namespace App\Http\Livewire\Modals\Admin\Events;

use App\Http\Livewire\WithAlert;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Post;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'édition et de visualisation d'un événement dans l'administration.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle de l'événement.
     *
     * @var \App\Models\Event
     */
    public $event;

    /**
     * Mode d'affichage ou d'édition ('view', 'edition', 'creation').
     *
     * @var string
     */
    public $mode;

    /**
     * Champs du formulaire.
     */
    public $title;
    public $description;
    public $event_type_id;
    public $start_date;
    public $start_time;
    public $end_date;
    public $end_time;
    public $location;
    public $post_id;

    /**
     * Indique si l'ajout est autorisé.
     *
     * @var bool
     */
    public $canAdd = true;

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    protected $listeners = ['render'];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type_id' => 'required|integer|exists:event_types,id',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:150',
            'post_id' => 'nullable|integer|exists:posts,id',
        ];
    }

    /**
     * Définit l'événement et initialise les champs du formulaire.
     *
     * @param int|null $id Identifiant de l'événement.
     */
    public function setEvent($id = null): void
    {
        $this->event = $this->event ?? Event::findOrNew($id);

        if ($this->event->exists) {
            $this->title = $this->event->title;
            $this->description = $this->event->description;
            $this->event_type_id = $this->event->event_type_id;
            $this->start_date = $this->event->start_date ? $this->event->start_date->format('Y-m-d') : null;
            $this->start_time = $this->event->start_time ? substr($this->event->start_time, 0, 5) : null;
            $this->end_date = $this->event->end_date ? $this->event->end_date->format('Y-m-d') : null;
            $this->end_time = $this->event->end_time ? substr($this->event->end_time, 0, 5) : null;
            $this->location = $this->event->location;
            $this->post_id = $this->event->post_id;
        } else {
            $this->title = null;
            $this->description = null;
            $this->event_type_id = EventType::first()?->id ?? null;
            $this->start_date = today()->format('Y-m-d');
            $this->start_time = null;
            $this->end_date = null;
            $this->end_time = null;
            $this->location = null;
            $this->post_id = null;
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.events',
            'withMarge' => true,
            'tabs' => [
                'general' => [
                    'icon' => 'calendar_today',
                    'title' => "Définir l'événement",
                    'hidden' => false,
                ],
            ],
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de l'événement et éventuellement le mode.
     */
    public function mount($data = []): void
    {
        $mode = $data['mode'] ?? 'view';
        $id = $data['id'] ?? null;

        $this->mode = $mode;
        $this->setEvent($id);
    }

    /**
     * Réinitialise les données depuis la base de données.
     */
    public function refresh(): void
    {
        $this->event = Event::findOrNew($this->event->id);
        $this->setEvent($this->event->id);
        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Réinitialisation effectuée avec succès.",
        ]);
    }

    /**
     * Définit l'onglet courant.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab): void
    {
        if ($this->$tabsSystem['currentTab'] === $tab) return;
        $this->$tabsSystem['currentTab'] = $tab;
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode): void
    {
        $this->mode = $mode;

        if ($mode === 'creation') {
            $this->event = null;
        }
        if ($mode !== 'view') {
            $this->setEvent($this->event?->id);
        }
    }

    /**
     * Enregistre l'événement (création ou modification).
     */
    public function save(): void
    {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->event->fill([
            'title' => $this->title,
            'description' => $this->description,
            'event_type_id' => $this->event_type_id,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time ?: null,
            'end_date' => $this->end_date ?: null,
            'end_time' => $this->end_time ?: null,
            'location' => $this->location ?: null,
            'post_id' => $this->post_id ?: null,
        ]);

        $this->event->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Création de l'événement effectuée avec succès.",
            ]);
        } else {
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Modification de l'événement effectuée avec succès.",
            ]);
        }

        $this->emit('render')->to('admin.events-manager');
    }

    /**
     * Rendu du composant.
     *
     * @param array|null $messageBag Sac de messages d'alerte.
     * @return \Illuminate\View\View
     */
    public function render($messageBag = null)
    {
        if ($messageBag) {
            $this->sendAlert($messageBag);
        }

        if ($this->mode === 'view') {
            return view('livewire.modals.admin.events.sheet', [
                'event' => $this->event,
            ]);
        }

        return view('livewire.modals.admin.models-form', [
            'addButtonTitle' => "Ajouter un événement",
            'eventTypes' => EventType::orderBy('name', 'asc')->get(),
            'availablePosts' => Post::where('published', true)->orderBy('id', 'desc')->limit(50)->get(),
        ]);
    }
}
