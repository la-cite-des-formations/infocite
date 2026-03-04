<?php

namespace App\Http\Livewire\Usage;

use App\Models\Chartnode;
use Livewire\Component;

/**
 * Composant Livewire pour l'affichage de l'organigramme (côté usage).
 */
class OrgChartManager extends Component
{
    /**
     * Indique si le composant a été rendu.
     *
     * @var bool
     */
    public $rendered = FALSE;

    /**
     * Indique si c'est le premier chargement.
     *
     * @var bool
     */
    public $firstLoad = TRUE;

    /**
     * Droit d'administration (toujours TRUE par défaut ici).
     *
     * @var bool
     */
    public $canAdmin = TRUE;

    /**
     * Rubrique courante.
     *
     * @var \App\Models\Rubric
     */
    public $rubric;

    /**
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant la rubrique.
     */
    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->rubric = $viewBag->rubric;
    }

    /**
     * Détermine si c'est le premier chargement du composant.
     */
    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    /**
     * Émet l'événement pour dessiner l'organigramme via Google Charts (côté client).
     */
    public function drawOrgChart() {
        $options = [
            'allowCollapse' => TRUE,
            'allowHtml'=> TRUE,
            'size'=> 'small',
            'compactRows' => TRUE,
        ];

        $this->emit('drawOrgChart', 'orgchart', Chartnode::getOrgChart(), $options);
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.org-chart-manager');
    }
}
