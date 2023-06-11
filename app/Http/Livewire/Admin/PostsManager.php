<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Post;
use App\Rubric;

class PostsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'posts';
    public $elements = 'posts';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'search' => '',
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'posts' => Post::filter($this->filter)
                ->orderByRaw('title ASC')
                ->paginate($this->perPage),
            'rubrics' => Rubric::all(),
        ]);
    }
}
