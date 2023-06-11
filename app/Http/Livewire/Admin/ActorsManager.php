<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Process;
use App\User;

class ActorsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'actors';
    public $elements = 'actors';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $actorInfo;
    public $filter = [
        'profiles' => FALSE,
        'search' => '',
        'groupType' => 'P+',
        'groupId' => '',
        'showUndefinedLinks' => TRUE,
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function mount() {
        $this->showFilter = TRUE;
        $this->updatedFilter();
    }

    public function updatedFilter() {
        $this->actorInfo = AP::getUserInfo($this->filter['groupType'], $this->filter['groupId']);
        $this->actorInfo['groupType'] = $this->filter['groupType'];
        $this->actorInfo['groupId'] = $this->filter['groupId'];
    }

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'processes' => Process::query()
                ->orderByRaw('name ASC')
                ->get(),
            'actors' => User::filter($this->filter)
                ->orderBy('name', 'ASC')
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
