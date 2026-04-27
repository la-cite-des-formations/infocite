<?php

namespace App\Http\Livewire;

use App\Charts\Charts;

/**
 * Trait pour l'intégration et le rafraîchissement des graphiques (Charts) via des événements Livewire.
 */
trait WithCharts
{
    /**
     * Dessine tous les graphiques définis dans la collection de stats.
     */
    public function drawAllCharts() {
        foreach($this->statsCollection as $stats) {
            $this->drawCharts($stats['charts'], $stats['filter'] ?? NULL);
        }
    }

    /**
     * Émet les événements pour dessiner une liste de graphiques.
     *
     * @param array $charts Liste des graphiques.
     * @param mixed $filter Filtre à appliquer.
     */
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

    /**
     * Alterne le libellé du bouton de détail.
     *
     * @param string $statsName Nom de la statistique.
     */
    public function toggleButton($statsName) {
        $this->statsCollection[$statsName]['buttonLabel'] =
            $this->statsCollection[$statsName]['buttonLabel'] == 'Détailler...' ?
                'Masquer...' :
                'Détailler...';
    }
}
