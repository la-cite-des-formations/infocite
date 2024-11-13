<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class ConnectionsManager extends Component
{
    public $models = 'connections';

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
