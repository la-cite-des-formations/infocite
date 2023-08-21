<?php

namespace App\Http\Livewire;

trait WithIconpicker

{
    public $searchIcons = '';

    public function choiceIcon($miName, string $model){
        if(isset($this->$model)){
            $this->$model->icon = $miName;
        }
    }
}
