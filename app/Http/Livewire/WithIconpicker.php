<?php

namespace App\Http\Livewire;

use App\CustomFacades\AP;

trait WithIconpicker

{
    public $searchIcons = '';

    public function getMiCodes() {
        $searchIcons = $this->searchIcons;

        return AP::getMaterialIconsCodes()
            ->when($searchIcons, function ($icons) use ($searchIcons) {
                return $icons->filter(function ($miCode, $miName) use ($searchIcons) {
                    return str_contains($miName, $searchIcons);
                });
            });
    }

    public function choiceIcon($miName, string $model){
        if(isset($this->$model)){
            $this->$model->icon = $miName;
        }
    }
}
