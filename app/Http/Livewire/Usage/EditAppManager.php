<?php

namespace App\Http\Livewire\Usage;

use App\App;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class EditAppManager extends Component
{
    use WithModal;
    use WithAlert;
    use WithIconpicker;

    public $backRoute;
    public $rubricRoute;
    public $mode;
    public $app;


    protected $listeners = ['modalClosed', 'save'];
    protected $rules = [
        'app.name' => 'required|string|max:255',
        'app.description' => 'required|string',
        'app.icon' => 'nullable|string|max:255',
        'app.url' => 'required|url|string|max:255'
    ];

    public function mount($viewBag) {
        $this->backRoute = session('appsBackRoute');
        $this->rubricRoute = '/'.$viewBag->rubricSegment;
        $this->mode = $viewBag->mode;
        $this->app = App::findOrNew($viewBag->app_id);
    }

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

    public function render() {
        return view('livewire.usage.edit-app-manager', [
            'icons' => $this->getMiCodes(),
        ]);
    }
}
