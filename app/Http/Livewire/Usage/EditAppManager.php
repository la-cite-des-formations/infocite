<?php

namespace App\Http\Livewire\Usage;

use App\App;
use Livewire\Component;

class EditAppManager extends Component
{
    public $backRoute;
    public $rubricRoute;
    public $mode;
    public $app;

    protected $rules = [
        'app.name' => 'required|string|max:255',
        'app.description' => 'required|string',
        'app.icon' => 'required|string|max:255',
        'app.url' => 'required|url|string|max:255',
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
        }
        else {
            // modification
            $this->app->save();
        }

        redirect($this->rubricRoute."/personal-apps/{$this->app->id}/edit") -> with('error','You have no permission for this page!');
    }

    public function render() {
        return view('livewire.usage.edit-app-manager');
    }
}
