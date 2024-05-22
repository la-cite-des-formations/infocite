<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Chartnode;

class ChartnodesManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'chartnodes';
    public $elements = 'chartnodes';

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
            'chartnodes' => Chartnode::filter($this->filter)
                ->orderBy('name', 'ASC')
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
