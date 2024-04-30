<?php

namespace App\Http\Livewire\Usage;

use App\Chartnode;
use Livewire\Component;

class OrgChartManager extends Component
{
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $canAdmin = TRUE;
    public $rubric;

    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->rubric = $viewBag->rubric;
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    public function drawOrgChart() {
        $orgChart = Chartnode::getOrgChart();

        $this->emit('drawOrgChart', 'small', $orgChart->pluck('data'), $orgChart->pluck('style'));
    }

    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.org-chart-manager');
    }
}
