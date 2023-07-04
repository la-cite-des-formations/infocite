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
    public $appId;
    public $message;


    public function mount($data) {
        extract($data);

        $this->handling = $handling;
        $this->commentId = $id ?? NULL;
        $this->appId = $appId ?? NULL;

        switch($handling){
            case 'deletePost':
                $this->message = "Êtes-vous sûr de vouloir supprimer cet article ?";
                break;
            case 'deleteComment':
                $this->message = "Êtes-vous sûr de vouloir supprimer ce commentaire ?";
                break;
            case 'deleteApp':
                $this->message = "Êtes-vous sûr de vouloir supprimer cette application ?";
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
        switch($this->handling){
            case('deletePost'):
                $this->emit('deletePost')->to('PostManager');
            break;
            case('deleteComment'):
                $this->emit('deleteComment', $this->commentId)->to('PostManager');
            break;
            case('deleteApp'):
                $this->emit('deleteApp', $this->appId)->to('AppsManager');
            break;
            case('update'):
            case('create'):
                $this->emit('save');
            break;
        }
    }

    public function render()
    {
        return view('livewire.modals.usage.confirm');
    }
}
