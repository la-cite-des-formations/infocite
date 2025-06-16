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
        $this->client = $modalBag['client'] ?? NULL;
        $this->modal = $modalBag['component'] ?? 'default-modal';
        $this->data = $modalBag['data'] ?? NULL;
        $this->filter = $modalBag['filter'] ?? NULL;

        $this->dispatchBrowserEvent('showModal');
    }

    public function render() {
        return view('livewire.modal-manager');
    }
}
