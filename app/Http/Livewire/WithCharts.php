<?php

namespace App\Http\Livewire;

use App\Charts\Charts;

trait WithCharts
{
    public function drawAllCharts() {
        foreach($this->statsCollection as $stats) {
            $this->drawCharts($stats['charts'], $stats['filter'] ?? NULL);
        }
    }

    public function drawCharts($charts, $filter) {
        foreach($charts as $chartName => $chart) {
            $this->emit(
                $chart['event'],
                $chart['target'],
                Charts::getChart($chartName, $filter),
                Charts::getChartOptions($chartName)
            );
        }
    }

    public function toggleButton($statsName) {
        $this->statsCollection[$statsName]['buttonLabel'] =
            $this->statsCollection[$statsName]['buttonLabel'] == 'Détailler...' ?
                'Masquer...' :
                'Détailler...';
    }
}
