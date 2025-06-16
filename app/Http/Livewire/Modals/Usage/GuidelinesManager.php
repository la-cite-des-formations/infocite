<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;

class GuidelinesManager extends Component
{
    public $subject;
    public $data;

    public function mount($modalBag) {
        $this->subject= $modalBag['subject'] ?? 'welcome';
        $this->data= $modalBag['data'] ?? [];
    }

    public function render()
    {
        return view('livewire.modals.usage.guidelines-manager');
    }
}
