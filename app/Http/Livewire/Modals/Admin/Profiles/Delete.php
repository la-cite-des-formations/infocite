<?php

namespace App\Http\Livewire\Modals\Admin\Profiles;

use App\Right;
use App\User;
use Livewire\Component;

class Delete extends Component
{
    public $profilesIDs;
    public $deletionPerformed = FALSE;

    public function mount($profilesIDs) {
        $this->profilesIDs = $profilesIDs;
    }

    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->profilesIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->profilesIDs)
                ->delete();
        });
        User::whereIn('id', $this->profilesIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->profilesIDs) > 1 ? 'Profils concernés' : 'Profil concerné',
            'models' => User::query()
                ->whereIn('id', $this->profilesIDs)
                ->orderByRaw('first_name ASC')
                ->get(),
            'modelInfo' => ['field' => 'first_name'],
        ]);
    }
}
