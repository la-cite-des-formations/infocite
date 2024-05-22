<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Group;

class GroupsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'groups';
    public $elements = 'groups';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'searchOnly' => FALSE,
        'search' => '',
        'type' => '',
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'groups' => Group::filter($this->filter)
                ->orderByRaw('name ASC')
                ->paginate($this->perPage),
        ]);
    }
}
