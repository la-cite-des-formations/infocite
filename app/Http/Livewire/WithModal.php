<?php

namespace App\Http\Livewire;

trait WithModal
{
    public $canAdd = TRUE;

    public function modalClosed() {
        $this->emit('render')->self();
    }

    public function showModal($action, $data = NULL) {
        $this->emit('show', [
            'component' => "admin.{$this->models}.$action",
            'data' => $data,
            'filter' => isset($this->filter) ? $this->filter : NULL,
        ])->to('modal-manager');
    }
}
