<?php

namespace App\Http\Livewire\Admin;

use App\Format;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;

class FormatsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'formats';
    public $elements = 'formats';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'search' => ''
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'formats' => Format::filter($this->filter)
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
