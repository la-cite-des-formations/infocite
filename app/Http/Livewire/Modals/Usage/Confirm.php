<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithModal;
use Livewire\Component;

class Confirm extends Component
{
    use WithModal;
    use WithAlert;

    public $handling;
    public $commentId;
    public $message;


    public function mount($data) {
        extract($data);

        $this->handling = $handling;
        $this->commentId = $id ?? NULL;
        switch($handling){
            case 'deletePost':
                $this->message = "Êtes-vous sûr de vouloir supprimer cet article ?";
                break;
            case 'deleteComment':
                $this->message = "Êtes-vous sûr de vouloir supprimer ce commentaire ?";
                break;
            case 'deleteApp':
                $this->message = "Êtes-vous sûr de vouloir supprimer cet application ?";
                break;
            case 'update':
                $this->message = "Êtes-vous sûr de vouloir modifier ?";
                break;
            case 'create':
                $this->message = "Êtes-vous sûr de vouloir créer ?";
                break;
        }
    }

    public function confirm() {
       if ($this->handling === 'deletePost'){
            $this->emit('deletePost')->to('PostManager');
        }elseif($this->handling === 'deleteComment'){
            $this->emit('deleteComment', $this->commentId)->to('PostManager');
            $this->dispatchBrowserEvent('hideModal');
        }elseif($this->handling === 'deleteApp'){
            $this->emit('deleteApp')->to('AppsManager');
        }elseif($this->handling === 'update'){
            $this->emit('save');
        }elseif($this->handling === 'create'){
            $this->emit('save');
        }
    // switch($this->handling){

    // }
    }

    public function render()
    {
        return view('livewire.modals.usage.confirm');
    }
}
