<?php

namespace App\Http\Livewire\Admin;

use App\User;
use Livewire\Component;

class UsingManager extends Component
{
    public $statsType = 'using';

    public $filter = [
        'searchOnly' => TRUE,
        'search' => ''
    ];

    public function render()
    {
        return view('livewire.admin.stats-viewer', [
            'editors' => User::editors(),
            'commentators' => User::commentators(),
            'personalAppUsers' => User::personalAppUsers(),
            'dashboard' => 'stats',
        ]);
    }
}
