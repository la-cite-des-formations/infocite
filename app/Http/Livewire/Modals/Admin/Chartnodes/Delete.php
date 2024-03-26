<?php

namespace App\Http\Livewire\Modals\Admin\Chartnodes;

use App\Chartnode;
use Livewire\Component;

class Delete extends Component
{
    public $chartnodessIDs;
    public $deletionPerformed = FALSE;

    public function mount($chartnodessIDs) {
        $this->chartnodessIDs = $chartnodessIDs;
    }

    public function delete() {
        Chartnode::whereIn('id', $this->chartnodessIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->chartnodessIDs) > 1 ? 'Noeuds graphiques concernés' : 'Noeud graphique concerné',
            'models' => Chartnode::query()
                ->whereIn('id', $this->chartnodessIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
