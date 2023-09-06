<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ModalManager extends Component
{
    protected $listeners = ['unload', 'show'];

    public $parent;
    public $modal = NULL;
    public $data;
    public $filter;
    public $client = NULL;

    public function mount($parent) {
        $this->parent = $parent;
    }

    public function unload() {
        if ($this->client) {
            $this->emit('modalClosed', $this->modal)->to($this->client);
        }
        $this->emit('modalClosed', $this->modal)->to($this->parent);
        $this->reset('modal', 'data', 'client');
    }

    public function show($modalBag) {
        extract($modalBag);
        $this->client = isset($client) ? $client : NULL;
        $this->modal = isset($component) ? $component : 'default-modal';
        $this->data = isset($data) ? $data : NULL;
        $this->filter = isset($filter) ? $filter : NULL;
        $this->dispatchBrowserEvent('showModal');
    }

    public function render() {
        return view('livewire.modal-manager');
    }
}
