<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Statistics\Connections;

/**
 * Composant Livewire pour la visualisation des statistiques de connexion dans l'interface d'administration.
 */
class ConnectionsManager extends Component
{
    use WithPagination;
    use WithCharts;

    /**
     * Thème de pagination utilisé (Bootstrap).
     *
     * @var string
     */
    protected $paginationTheme = 'bootstrap';

    /**
     * Page de statistiques actuelle.
     *
     * @var string
     */
    public $statsPage = 'connections';
    /**
     * Configuration des collections de statistiques (par jour, par mois).
     *
     * @var array
     */
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
            'buttonLabel' => 'Détailler...',
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
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [5, 10, 15, 25],
            'perPage' => 10,
        ],
    ];
    /**
     * Configuration des onglets de graphiques.
     *
     * @var array
     */
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

    /**
     * Définit l'onglet courant et affiche les graphiques associés.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
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


    /**
     * Met à jour les graphiques de connexions par jour lors du changement de filtre.
     */
    public function updatedStatsCollectionConnectionsByDayFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['connectionsByDay']['charts'],
            $this->statsCollection['connectionsByDay']['filter']
        );

        $this->resetPage('connectionsByDayPage');
    }

    /**
     * Met à jour les graphiques de connexions par mois lors du changement de filtre.
     */
    public function updatedStatsCollectionConnectionsByMonthFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['connectionsByMonth']['charts'],
            $this->statsCollection['connectionsByMonth']['filter']
        );

        $this->resetPage('connectionsByMonthPage');
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
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
