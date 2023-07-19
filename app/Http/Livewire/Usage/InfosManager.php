<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;
use App\Post;
use App\Rubric;
use App\User;

class InfosManager extends Component
{
    public $rubric;
    public function mount($viewBag) {
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
    }
    public function render() {
        return view('livewire.usage.infos-manager', [
            'user' => User::find(auth()->user()->id),
        ]);
    }
}
