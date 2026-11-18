<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithModal;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Post;
use App\Models\Rubric;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Composant Livewire pour la création et la modification d'un événement autonome dans l'agenda.
 */
class EditEventManager extends Component
{
    use WithFileUploads;
    use WithModal;
    use WithAlert;

    /**
     * Instance de l'événement en cours d'édition.
     *
     * @var Event
     */
    public $event;

    /**
     * Mode ('creation' ou 'edition').
     *
     * @var string
     */
    public $mode = 'creation';

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
     * Gestion de l'image et du cadrage (point focal).
     */
    public $uploadedImage;
    public $existingImage = null;
    /** URL temporaire de l'image uploadée (mise à jour automatiquement). */
    public $uploadedImageUrl = null;
    public float $focalX = 50.0;
    public float $focalY = 50.0;

    /**
     * Recherche d'article pour le champ post_id.
     */
    public $postSearch = '';

    /**
     * Rubrique Agenda parente.
     */
    public $agendaRubric;

    protected $listeners = [
        'focalPointUpdated' => 'applyFocalPoint',
        'save' => 'save',
    ];

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
            'uploadedImage' => 'nullable|image|max:10240',
        ];
    }

    public function mount($viewBag)
    {
        $this->mode = $viewBag->mode ?? 'creation';
        $this->agendaRubric = $viewBag->rubric ?? Rubric::where('segment', 'agenda')->first();

        if ($this->mode === 'edition' && !empty($viewBag->event_id)) {
            $this->event = Event::findOrFail($viewBag->event_id);
            $this->title = $this->event->title;
            $this->description = $this->event->description;
            $this->event_type_id = $this->event->event_type_id;
            $this->start_date = $this->event->start_date ? $this->event->start_date->format('Y-m-d') : null;
            $this->start_time = $this->event->start_time ? substr($this->event->start_time, 0, 5) : null;
            $this->end_date = $this->event->end_date ? $this->event->end_date->format('Y-m-d') : null;
            $this->end_time = $this->event->end_time ? substr($this->event->end_time, 0, 5) : null;
            $this->location = $this->event->location;
            $this->post_id = $this->event->post_id;

            if (!empty($this->event->image) && is_array($this->event->image)) {
                $this->existingImage = $this->event->image;
                $fp = $this->event->getFocalPoint();
                $this->focalX = $fp['x'];
                $this->focalY = $fp['y'];
            }
        } else {
            $this->event = new Event();
            $this->start_date = today()->format('Y-m-d');
        }
    }

    /**
     * Déclenché automatiquement par Livewire après réception du fichier uploadé.
     * Stocke l'URL temporaire dans une propriété publique accessible dans le blade.
     */
    public function updatedUploadedImage(): void
    {
        $this->uploadedImageUrl = $this->uploadedImage ? $this->uploadedImage->temporaryUrl() : null;
    }

    /**
     * Applique les nouvelles coordonnées du point focal.
     */
    public function applyFocalPoint(float $x, float $y): void
    {
        $this->focalX = round(max(0, min(100, $x)), 2);
        $this->focalY = round(max(0, min(100, $y)), 2);

        if ($this->existingImage) {
            $this->existingImage['focal_point'] = [
                'x' => $this->focalX,
                'y' => $this->focalY,
            ];
        }
    }

    /**
     * Réinitialise l'image uploadée ou existante.
     */
    public function removeImage(): void
    {
        $this->uploadedImage = null;
        $this->uploadedImageUrl = null;
        $this->existingImage = null;
        $this->focalX = 50.0;
        $this->focalY = 50.0;
    }

    /**
     * Sauvegarde l'événement (création ou mise à jour).
     */
    public function save()
    {
        $this->validate();

        $imageData = $this->existingImage;

        // Traitement d'une nouvelle image uploadée
        if ($this->uploadedImage) {
            $disk = Storage::disk('public');
            $folder = 'events';
            if (!$disk->exists($folder)) {
                $disk->makeDirectory($folder);
            }

            $manager = new ImageManager(new Driver());
            $imageFile = $manager->read($this->uploadedImage->getRealPath());
            $filename = 'evt_' . time() . '_' . uniqid() . '.' . $this->uploadedImage->getClientOriginalExtension();
            $path = $folder . '/' . $filename;
            $fullPath = storage_path('app/public/' . $path);

            $imageFile->scaleDown(1200, 1200);
            $imageFile->save($fullPath, quality: 80);

            $imageData = [
                'path' => '/storage/' . $path,
                'filename' => $this->uploadedImage->getClientOriginalName(),
                'thumbnail' => '/storage/' . $path,
                'focal_point' => [
                    'x' => $this->focalX,
                    'y' => $this->focalY,
                ],
            ];
        } elseif ($imageData) {
            // Mise à jour du focal point si l'image existante a été conservée
            $imageData['focal_point'] = [
                'x' => $this->focalX,
                'y' => $this->focalY,
            ];
        }

        $this->event->fill([
            'title' => $this->title,
            'description' => $this->description,
            'image' => $imageData,
            'event_type_id' => $this->event_type_id,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time ?: null,
            'end_date' => $this->end_date ?: null,
            'end_time' => $this->end_time ?: null,
            'location' => $this->location ?: null,
            'post_id' => $this->post_id ?: null,
        ]);

        $this->event->save();

        $action = $this->mode === 'creation' ? 'créé' : 'mis à jour';
        session()->flash('alertClass', 'success');
        session()->flash('message', "L'événement <strong>{$this->title}</strong> a été {$action} avec succès.");

        $redirectRoute = $this->agendaRubric ? route('rubric.index', ['rubric' => $this->agendaRubric->segmentPath()]) : '/';
        return redirect($redirectRoute);
    }

    public function render()
    {
        // Articles disponibles pour association (les plus récents publiés)
        $postsQuery = Post::query()
            ->where('published', true)
            ->orderBy('id', 'desc');

        if (!empty($this->postSearch)) {
            $postsQuery->where('title', 'like', '%' . $this->postSearch . '%');
        }

        $availablePosts = $postsQuery->limit(50)->get();

        return view('livewire.usage.edit-event-manager', [
            'eventTypes' => EventType::orderBy('name', 'asc')->get(),
            'availablePosts' => $availablePosts,
        ]);
    }
}
