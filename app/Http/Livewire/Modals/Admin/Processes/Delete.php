<?php

namespace App\Http\Livewire\Modals\Admin\Processes;

use App\Process;
use Livewire\Component;

class Delete extends Component
{
    public $processesIDs;
    public $deletionPerformed = FALSE;

    public function mount($processesIDs) {
        $this->processesIDs = $processesIDs;
    }

    public function delete() {
        Process::whereIn('id', $this->processesIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->processesIDs) > 1 ? 'Processus fonctionnels concernées' : 'Processus fonctionnel concerné',
            'models' => Process::query()
                ->whereIn('id', $this->processesIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
