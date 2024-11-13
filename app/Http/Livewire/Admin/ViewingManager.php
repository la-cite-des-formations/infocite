<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class ViewingManager extends Component
{
    public $models = 'viewing';

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
