<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithModal;
use Livewire\Component;

class Confirm extends Component
{
    use WithModal;
    use WithAlert;

    public $handling;



    public function mount($data) {
        extract($data);

        $this->handling = $handling;
        $this->id = $id ?? NULL;
    }

    public function confirm() {
       if ($this->handling === 'deletePost'){
            $this->emit('deletePost')->to('PostManager');
        }elseif($this->handling === 'deleteComment'){
            $this->emit('deleteComment')->to('PostManager');
        }elseif($this->handling === 'deleteApp'){
            $this->emit('deleteApp')->to('AppsManager');
        }elseif($this->handling === 'modifier'){
            $this->emit('save');
        }elseif($this->handling === 'create'){
            $this->emit('save');
        }
    }

    public function render()
    {
        return view('livewire.modals.usage.confirm');
    }
}
