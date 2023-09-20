<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;

class OrgChartManager extends Component
{
    public $firstLoad = TRUE;
    public $canAdmin = TRUE;
    public $orgChartTitle;
    public $rubric;

    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->rubric = $viewBag->rubric;
    }

    public function hydrate() {
        if ($this->firstLoad) $this->firstLoad = FALSE;
    }

    public function drawOrgChart($type = 'Process') {
        $this->orgChartTitle = $type == 'Process' ? "Organigramme procédural" : "Organigramme relationnel";

        $orgChart = ("App\\{$type}")::getOrgChart();

        $this->emit('drawOrgChart', 'small', $orgChart->pluck('data'), $orgChart->pluck('style'));
    }

    public function render() {
        return view('livewire.usage.org-chart-manager');
    }
}
