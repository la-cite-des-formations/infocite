<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Group;
use App\User;
use App\Roles;

class ProfilesManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'profiles';
    public $elements = 'profiles';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $userInfo;
    public $groupFilter;
    public $filter = [
        'searchOnly' => TRUE,
        'profiles' => TRUE,
        'search' => '',
        'groupType' => '',
        'groupId' => '',
        'isFrozen' => '',
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function mount() {
        $this->updatedFilter();
        $this->groupFilter = AP::getGroupFilter();
    }

    public function updatingFilterGroupType() {
        $this->filter['groupId'] = '';
    }

    public function updatedFilterGroupType() {
        $this->groupFilter = AP::getGroupFilter($this->filter['groupType']);
    }

    public function updatedFilter() {

        $this->userInfo = AP::getUserInfo($this->filter['groupType'], $this->filter['groupId']);
        $this->userInfo['groupType'] = $this->filter['groupType'];
        $this->userInfo['groupId'] = $this->filter['groupId'];
    }

    public function render()
    {
        $groupType = $this->filter['groupType'];

        return view('livewire.admin.models-manager', [
            'groups' => Group::query()
                ->when($groupType, function ($query) use ($groupType) {
                    $query->where('type', $groupType);
                })
                ->orderByRaw('name ASC')
                ->get(),
            'profiles' => User::filter($this->filter)
                ->orderByRaw('first_name ASC')
                ->paginate($this->perPage),
        ]);
    }
}
