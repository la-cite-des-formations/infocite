<?php

namespace App\Http\Livewire;

trait WithUsageMode
{
    public $mode;

    public function setMode() {
        $this->mode = session('mode', 'view');
        session(['mode' => $this->mode]);
    }

    public function switchMode() {
        $this->mode = $this->mode == 'view' ? 'edition' : 'view';
        session(['mode' => $this->mode]);
    }
}
