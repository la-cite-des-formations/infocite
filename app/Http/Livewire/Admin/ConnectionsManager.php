<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Statistics\Connections;

class ConnectionsManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'connections';
    public $statsCollection = [
        'connectionsByDay' => [
            'filter' => [
                'userType' => 'all',
            ],
            'charts' => [
                'connectionsByDay' => [
                    'target' => 'connectionsByDayChart',
                    'event' => 'drawBarChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [5, 10, 15, 25],
            'perPage' => 10,
        ],
        'connectionsByMonth' => [
            'filter' => [
                'userType' => 'all',
            ],
            'charts' => [
                'connectionsByMonth' => [
                    'target' => 'connectionsByMonthChart',
                    'event' => 'drawBarChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [5, 10, 15, 25],
            'perPage' => 10,
        ],
    ];
    public $chartTabs = [
        'name' => 'chartTabs',
        'currentTab' => 'connections-by-day',
        'panesPath' => 'includes.admin.stats.connection',
        'withMarge' => TRUE,
        'tabs' => [
            'connections-by-day' => [
                'icon' => 'today',
                'title' => "Connexions par jour",
                'hidden' => FALSE,
            ],
            'connections-by-month' => [
                'icon' => 'calendar_month',
                'title' => "Connexions par mois",
                'hidden' => FALSE,
            ],
        ],
    ];

    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        match ($tab) {
            'connections-by-day' => $collection = "connectionsByDay",
            'connections-by-month' => $collection = "connectionsByMonth",
            default => $collection = "connectionByDay"
        };

        $this->drawCharts(
            $this->statsCollection[$collection]['charts'],
            $this->statsCollection[$collection]['filter']
        );

        $this->resetPage("{$collection}Page");
    }


    public function updatedStatsCollectionConnectionsByWeekFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['connectionsByDay']['charts'],
            $this->statsCollection['connectionsByDay']['filter']
        );

        $this->resetPage('connectionsByDayPage');
    }

    public function updatedStatsCollectionConnectionsByMonthFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['connectionsByMonth']['charts'],
            $this->statsCollection['connectionsByMonth']['filter']
        );

        $this->resetPage('connectionsByMonthPage');
    }

    public function render()
    {
        return view('livewire.admin.stats-viewer', [
            'connectionsByDay' => Connections::groupByDay($this->statsCollection['connectionsByDay']['filter'])
                ->paginate($this->statsCollection['connectionsByDay']['perPage'], 'connectionsByDayPage'),
            'connectionsByMonth' => Connections::groupByMonth($this->statsCollection['connectionsByMonth']['filter'])
                ->paginate($this->statsCollection['connectionsByMonth']['perPage'], 'connectionsByMonthPage'),
            'dashboard' => 'stats',
        ]);
    }
}
