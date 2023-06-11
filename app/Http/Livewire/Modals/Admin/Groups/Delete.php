<?php

namespace App\Http\Livewire\Modals\Admin\Groups;

use App\Group;
use App\Right;
use Livewire\Component;

class Delete extends Component
{
    public $groupsIDs;
    public $deletionPerformed = FALSE;

    public function mount($groupsIDs) {
        $this->groupsIDs = $groupsIDs;
    }

    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'Group')
                ->whereIn('resource_id', $this->groupsIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'Group')
                ->whereIn('resource_id', $this->groupsIDs)
                ->delete();
        });
        Group::whereIn('id', $this->groupsIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->groupsIDs) > 1 ? 'Groupes concernés' : 'Groupe concerné',
            'models' => Group::query()
                ->whereIn('id', $this->groupsIDs)
                ->orderByRaw('type ASC, name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
