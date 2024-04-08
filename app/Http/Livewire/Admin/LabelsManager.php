<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\User;

class LabelsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'labels';
    public $elements = 'referents';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'search' => '',
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    private function getReferents() {
        $search = strtolower($this->filter['search']);
        $referents = User::allWho('have-label')
            ->get()
            ->when($search, function ($referents) use ($search) {
                return $referents->filter(function ($referent) use ($search) {
                    return
                        str_contains(strtolower($referent->label), $search) ||
                        str_contains(strtolower($referent->identity), $search) ||
                        str_contains(strtolower($referent->process), $search);
                });
            });

        if ($referents->isEmpty()) {
            return User::query()->whereNull('id');
        }

        return $referents->toQuery();
    }

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'referents' => $this
                ->getReferents()
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
