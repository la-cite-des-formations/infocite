<?php

namespace App\Http\Livewire;

trait WithFilter
{
    public $showFilter = FALSE;

    public function updatedWithFilter()
    {
        $this->resetPage();
    }

    public function toggleFilter() {
        $this->showFilter = !$this->showFilter;
    }
}
