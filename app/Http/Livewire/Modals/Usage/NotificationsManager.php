<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithModal;
use Livewire\Component;

class NotificationsManager extends Component
{
    use WithModal;
    public $perPage = 8;


    public function mount($data) {
        extract($data);

    }


    public function render()
    {
        return view('livewire.modals.usage.notifications-manager', [

        ]);
    }
}
