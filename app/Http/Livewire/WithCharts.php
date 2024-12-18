<?php

namespace App\Http\Livewire;

use App\Stats;

trait WithCharts
{
    public function drawAllCharts() {
        foreach($this->statsCollection as $stats) {
            $this->drawCharts($stats['charts'], $stats['filter']);
        }
    }

    public function drawCharts($charts, $filter) {
        foreach($charts as $chartName => $chart) {
            $this->emit(
                $chart['event'],
                $chart['target'],
                Stats::getChart($chartName, $filter),
                Stats::getChartOptions($chartName)
            );
        }
    }

    public function toggleButton($statsName) {
        $this->statsCollection[$statsName]['buttonLabel'] =
            $this->statsCollection[$statsName]['buttonLabel'] == 'Voir plus...' ?
                'Voir moins...' :
                'Voir plus...';
    }
}
