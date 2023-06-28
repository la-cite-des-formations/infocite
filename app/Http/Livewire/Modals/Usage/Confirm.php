<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithModal;
use Livewire\Component;

class Confirm extends Component
{
    use WithModal;
    protected $listeners = ['modalClosed', 'render'];
    public function confirm()
    {

    }
    public function render()
    {
        return view('livewire.modals.usage.confirm', [

        ]);
    }
}
