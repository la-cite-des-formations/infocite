<?php

namespace App\Http\Livewire;

trait WithModal
{
    public $canAdd = TRUE;

    public function modalClosed() {
        $this->emit('render')->self();
    }

    public function showModal($action, $data = NULL) {
        if($action == 'confirm'){
            $component = "usage.confirm";
        }
        else{
            $component = "admin.{$this->models}.$action";
        }
        $this->emit('show', [
            'component' => $component,
            'data' => $data,
            'filter' => isset($this->filter) ? $this->filter : NULL,
        ])->to('modal-manager');
    }
}
