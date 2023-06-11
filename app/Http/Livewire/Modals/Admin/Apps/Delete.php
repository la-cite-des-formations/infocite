<?php

namespace App\Http\Livewire\Modals\Admin\Apps;

use App\App;
use App\Right;
use Livewire\Component;

class Delete extends Component
{
    public $appsIDs;
    public $deletionPerformed = FALSE;

    public function mount($appsIDs) {
        $this->appsIDs = $appsIDs;
    }

    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'App')
                ->whereIn('resource_id', $this->appsIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'App')
                ->whereIn('resource_id', $this->appsIDs)
                ->delete();
        });
        App::whereIn('id', $this->appsIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->appsIDs) > 1 ? 'Applications concernées' : 'Application concernée',
            'models' => App::query()
                ->whereIn('id', $this->appsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
