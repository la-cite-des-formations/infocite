<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Right;

class RightsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'rights';
    public $elements = 'rights';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'searchOnly' => TRUE,
        'search' => ''
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'rights' => Right::filter($this->filter)
                ->paginate($this->perPage),
        ]);
    }
}
