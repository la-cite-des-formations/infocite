<?php

namespace App\Http\Livewire\Modals\Admin\Processes;

use App\Format;
use App\Group;
use App\Http\Livewire\WithAlert;
use App\Process;
use Livewire\Component;

class Build extends Component
{
    use WithAlert;

    public $formatId = NULL;
    public $rank = 0;

    protected $rules = [
        'formatId' => 'required',
    ];

    public function build() {
        $groups = Group::query()
            ->where('type', 'P')
            ->orderBy('name', 'ASC')
            ->get()
            ->filter(function ($group) {
                return is_null($group->process);
            });

        $this->validate();

        foreach($groups as $group) {
            $this->rank++;

            (new Process([
                'name' => $group->name,
                'group_id' => $group->id,
                'format_id' => $this->formatId,
                'rank' => $this->rank,
            ]))->save();
        }

        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Génération automatique des processus fonctionnels terminée avec succès.",
        ]);
    }

    public function render() {
        return view('livewire.modals.admin.processes.build', [
            'groups' => Group::query()
                ->where('type', 'P')
                ->orderBy('name', 'ASC')
                ->get()
                ->filter(function ($group) {
                    return is_null($group->process);
                }),
            'processes' => Process::all(),
            'formats' => Format::orderBy('name')->get(),
        ]);
    }
}
