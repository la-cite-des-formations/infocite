<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Models\Connection;

class ConnectionsManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'connections';
    public $statsCollection = [
        'connections' => [
            'filter' => [
                'userType' => 'all',
            ],
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

    public function updatedStatsCollectionConnectionsFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['connections']['charts'],
            $this->statsCollection['connections']['filter']
        );

        $this->resetPage('connectionsPage');
    }

    public function render()
    {
        return view('livewire.admin.stats-viewer', [
            'connections' => Connection::allGroupByDate($this->statsCollection['connections']['filter'])
                ->paginate($this->statsCollection['connections']['perPage'], 'connectionsPage'),
            'dashboard' => 'stats',
        ]);
    }
}
