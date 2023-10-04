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
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;

    protected $listeners = ['deleteApp', 'render'];

    public function mount($viewBag) {
        $this->rubricSegment = $viewBag->rubricSegment;
    }

    public function deleteApp($appId) {
        App::find($appId)->delete();
    }

    public function redirectToApp($appUrl) {
        $this->firstLoad = FALSE;

        if (!$this->blockRedirection) {
            $this->emit('newTabRedirection', $appUrl);
        }
        else {
            $this->blockRedirection = FALSE;
        }
    }

    public function blockRedirection() {
        $this->firstLoad = FALSE;
        $this->blockRedirection = TRUE;
    }

    public function render() {
        return view('livewire.usage.apps-manager');
    }
}
