<?php

namespace App\Http\Livewire\Modals\Admin\Users;

use App\Models\Right;
use App\Models\User;
use Livewire\Component;

class Delete extends Component
{
    public $usersIDs;
    public $deletionPerformed = FALSE;

    public function mount($usersIDs) {
        $this->usersIDs = $usersIDs;
    }

    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->usersIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'User')
                ->whereIn('resource_id', $this->usersIDs)
                ->delete();
        });
        User::whereIn('id', $this->usersIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->usersIDs) > 1 ? 'Utilisateurs concernés' : 'Utilisateur concerné',
            'models' => User::query()
                ->whereIn('id', $this->usersIDs)
                ->orderByRaw('name ASC, first_name ASC')
                ->get(),
            'modelInfo' => ['function' => 'identity'],
        ]);
    }
}
