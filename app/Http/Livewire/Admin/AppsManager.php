<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\App;

class AppsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'apps';
    public $elements = 'apps';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'authType' => '',
        'type' => 'I', // applications institutionnelles par défaut
        'search' => '',
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'apps' => App::filter($this->filter)
                ->paginate($this->perPage),
        ]);
    }
}
