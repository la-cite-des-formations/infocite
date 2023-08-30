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

    public function showModal($action, $data = NULL) {
        switch ($action) {
            case 'confirm' :
                $component = "usage.confirm";
            break;
            case 'notify' :
                $this->firstLoad = FALSE;
                $component = "usage.notifications-manager";
            break;
            default :
                $component = "admin.{$this->models}.$action";
        }

        $this->emit('show', [
            'component' => $component,
            'data' => $data,
            'filter' => isset($this->filter) ? $this->filter : NULL,
        ])->to('modal-manager');
    }
}
