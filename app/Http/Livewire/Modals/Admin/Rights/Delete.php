<?php

namespace App\Http\Livewire\Modals\Admin\Rights;

use App\Right;
use Livewire\Component;

class Delete extends Component
{
    public $rightsIDs;
    public $deletionPerformed = FALSE;

    public function mount($rightsIDs) {
        $this->rightsIDs = $rightsIDs;
    }

    public function delete() {
        Right::whereIn('id', $this->rightsIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->rightsIDs) > 1 ? 'Droits concernés' : 'Droit concerné',
            'models' => Right::query()
                ->whereIn('id', $this->rightsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'description'],
        ]);
    }
}
