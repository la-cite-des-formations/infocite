<?php

namespace App\Http\Livewire;

trait WithModal
{
    public $canAdd = TRUE;

    public function modalClosed() {
        if (isset($this->closedModalCallback)) {
            foreach($this->closedModalCallback as $function) {
                $this->$function();
            }
        }
        $this->emit('render')->self();
    }

    public function showModal($modal, $data = NULL) {
        switch ($modal) {
            case 'confirm' :
                $component = "usage.confirm";
                break;

            case 'notify' :
                $component = "usage.notifications-manager";
                break;

            case 'guidelines':
                $component = "usage.guidelines-manager";
                break;

            default :
                $component = "admin.{$this->models}.$modal";
        }

        $this->emit('show', [
            'component' => $component,
            'data' => $data,
            'filter' => $this->filter ?? NULL,
        ])->to('modal-manager');
    }
}
