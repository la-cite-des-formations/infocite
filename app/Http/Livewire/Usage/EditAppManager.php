<?php

namespace App\Http\Livewire\Usage;

use App\App;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithIconpicker;
use App\Http\Livewire\WithModal;
use Livewire\Component;

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
        'app.icon' => 'required|string|max:255',
        'app.url' => 'required|url|string|max:255'
    ];

    public function mount($viewBag) {
        $this->backRoute = $viewBag->backRoute;
        $this->rubricRoute = '/'.$viewBag->rubricSegment;
        $this->mode = $viewBag->mode;
        $this->app = App::findOrNew($viewBag->app_id);
    }

    public function save() {
        $this->validate();

        if ($this->mode === 'creation') {
            // création
            $this->app->owner_id = auth()->user()->id;
            $this->app->save();
            $this->app->users()->attach(auth()->user()->id);
            $this
            ->sendAlert([
                'alertClass' => 'success',
                'message' => "Création de la mise en forme effectuée avec succès."
            ]);
        }
        else {
            // modification
            $this->app->save();
            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Modification de la mise en forme effectuée avec succès."
                ]);
        }

        redirect($this->rubricRoute."/personal-apps/{$this->app->id}/edit");
    }

    public function render() {
        $searchIcons = $this->searchIcons;
        return view('livewire.usage.edit-app-manager', [
            'icons' => AP::getMaterialIconsCodes()
                ->when($searchIcons, function ($icons) use ($searchIcons) {
                    return $icons->filter(function ($miCode, $miName) use ($searchIcons) {
                        return str_contains($miName, $searchIcons);
                    });
                }),
        ]);
    }
}
