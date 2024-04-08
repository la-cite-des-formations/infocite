<?php

namespace App\Http\Livewire\Modals\Admin\Referents;

use App\User;
use Livewire\Component;

class Edit extends Component
{
    public $referent;

    public function setReferent($id = NULL) {
        $this->referent = $this->referent ?? User::findOrNew($id);
    }

    public function mount($data) {
        extract($data);

        $this->setReferent($id ?? NULL);
    }


    public function render(){
        return view('livewire.modals.admin.referents.sheet');
    }
}
