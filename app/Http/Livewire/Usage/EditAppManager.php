<?php

namespace App\Http\Livewire\Usage;

use App\Models\App;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

/**
 * Composant Livewire pour l'édition d'une application existante ou la création d'une nouvelle.
 */
class EditAppManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithIconpicker;

    /**
     * Route de retour après enregistrement.
     *
     * @var string
     */
    public $backRoute;

    /**
     * Route de la rubrique parente.
     *
     * @var string
     */
    public $rubricRoute;

    /**
     * Mode d'édition ('creation' ou 'edition').
     *
     * @var string
     */
    public $mode;

    /**
     * Instance de l'application en cours d'édition.
     *
     * @var \App\Models\App
     */
    public $app;


    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['modalClosed', 'save'];

    /**
     * Règles de validation pour l'application.
     *
     * @var array
     */
    protected $rules = [
        'app.name' => 'required|string|max:255',
        'app.description' => 'required|string',
        'app.icon' => 'nullable|string|max:255',
        'app.url' => 'required|url|string|max:255'
    ];

    /**
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant le mode et l'ID de l'application.
     */
    public function mount($viewBag) {
        $this->backRoute = session('appsBackRoute');
        $this->rubricRoute = '/'.$viewBag->rubricSegment;
        $this->mode = $viewBag->mode;
        $this->app = App::findOrNew($viewBag->app_id);
    }

    /**
     * Enregistre les modifications ou crée la nouvelle application.
     * Récupère automatiquement le favicon du domaine.
     */
    public function save() {
        $this->validate();

        $this->app->favicon = HTTP::
            get("https://www.google.com/s2/favicons?domain=" . $this->app->url)
            ->header("Content-Location");

        if (empty($this->app->favicon) && empty($this->app->icon)) {
            $this->sendAlert([
                'alertClass' => 'danger',
                'message' => "Aucun favicon trouvé, sélectionnez une icône s'il-vous-plaît."
            ]);
            return;
        }

        if ($this->mode === 'creation') {
            // création
            $this->app->owner_id = auth()->user()->id;
            $this->app->save();
            $this->app->users()->attach(auth()->user()->id);

            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Ajout de l'application effectuée avec succès."
            ]);
        }
        else {
            // modification
            $this->app->save();

            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Modification de l'application'e effectuée avec succès."
            ]);
        }

        session(['backRoute' => $this->backRoute]);

        redirect()->route(
            'personal-apps.edit',
            [
                'app_id' => $this->app->id,
            ]
        );
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.usage.edit-app-manager', [
            'icons' => $this->getMiCodes(),
        ]);
    }
}
