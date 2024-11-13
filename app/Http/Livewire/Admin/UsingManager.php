<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class UsingManager extends Component
{
    public $models = 'using';

    public $filter = [
        'searchOnly' => TRUE,
        'search' => ''
    ];

    public function render()
    {
        return view('livewire.admin.stats-viewer', [
            'dashboard' => 'stats',
        ]);
    }
}
