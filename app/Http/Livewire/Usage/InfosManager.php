<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;
use App\Post;
use App\Rubric;
use App\User;

class InfosManager extends Component
{
    public $rubric;
    // public $user;
    // public $userNbClasses;
    // public $truncateClassesList;
    public function mount($viewBag) {
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        // $this->userNbClasses = $this->user->groups(['C', 'E'])->count();
        // $this->truncateClassesList = $this->userNbClasses > $this->classesMin;
    }
    public function render() {
        return view('livewire.usage.infos-manager', [
            'user' => User::find(auth()->user()->id),
        ]);
    }
}
