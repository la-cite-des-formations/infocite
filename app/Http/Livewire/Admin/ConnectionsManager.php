<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Connection;

class ConnectionsManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'connections';
    public $statsCollection = [
        'connections' => [
            'filter' => [],
            'charts' => [
                'connections' => [
                    'target' => 'connectionsChart',
                    'event' => 'drawBarChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [5, 10, 15, 25],
            'perPage' => 10,
        ],
    ];

    public function render()
    {
        return view('livewire.admin.stats-viewer', [
            'connections' => Connection::allGroupByDate()
                ->paginate($this->statsCollection['connections']['perPage']),
            'dashboard' => 'stats',
        ]);
    }
}
