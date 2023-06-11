<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Rubric;

class RubricsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'rubrics';
    public $elements = 'rubrics';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'search' => '',
        //'is_parent' => NULL,
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'rubrics' => Rubric::filter($this->filter)
                ->orderByRaw('position ASC, rank ASC')
                ->paginate($this->perPage),
        ]);
    }
}
