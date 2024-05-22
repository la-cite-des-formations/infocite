<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;

trait WithSearching
{
    public static function tableContains($tableElements, $searchedString) {
        foreach($tableElements as $element) {
            if (Str::of(Str::slug($element))->contains(Str::slug($searchedString))) return TRUE;
        }
        return FALSE;
    }
}
