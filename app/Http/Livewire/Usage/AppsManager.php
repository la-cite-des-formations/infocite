<?php

namespace App\Http\Livewire\Usage;

use App\App;
use App\Http\Livewire\WithModal;
use Livewire\Component;
use Livewire\WithPagination;

class AppsManager extends Component
{
    use WithPagination;
    use WithModal;

    public $rubricSegment;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;

    protected $listeners = ['deleteApp', 'render'];

    public function mount($viewBag) {
        $this->rubricSegment = $viewBag->rubricSegment;
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    public function deleteApp($appId) {
        App::find($appId)->delete();
    }

    public function redirectToApp($appUrl) {
        if (!$this->blockRedirection) {
            $this->emit('newTabRedirection', $appUrl);
        }
        else {
            $this->blockRedirection = FALSE;
        }
    }

    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.apps-manager');
    }
}
