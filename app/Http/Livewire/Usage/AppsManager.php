<?php

namespace App\Http\Livewire\Usage;

use App\App;
use Livewire\Component;
use Livewire\WithPagination;

class AppsManager extends Component
{
    use WithPagination;

    public $rubricSegment;
    public $firstLoad = TRUE;

    protected $listeners = ['render'];

    public function mount($viewBag) {
        $this->rubricSegment = $viewBag->rubricSegment;
    }

    public function deleteApp($appId) {
        App::find($appId)->delete();
    }

    public function render() {
        return view('livewire.usage.apps-manager');
    }
}
