<?php

namespace App\Http\Livewire;

trait WithIconpicker

{
    public $searchIcons = '';

    public function choiceIcon($miName){
        $this->post->icon = $miName;
    }
}
