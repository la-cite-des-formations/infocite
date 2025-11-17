<?php

namespace App\Http\Livewire;

use App\CustomFacades\AP;
use Illuminate\Support\Facades\Cookie;

trait WithIconpicker

{
    public $searchIcons = '';

    public function getMiCodes() {
        $searchIcons = $this->searchIcons;

        return AP::getMiCodes()
            ->when($searchIcons, function ($icons) use ($searchIcons) {
                return $icons->filter(function ($miCode, $miName) use ($searchIcons) {
                    return str_contains($miName, $searchIcons) || str_contains($miCode, $searchIcons);
                });
            });
    }

    public function choiceIcon(string $miName, string $model) {
        if(isset($this->$model)) {
            $this->$model->icon = $miName;
            Cookie::queue(
                'recentMiCodes',
                AP::getRecentMiCodes()
                    ->where('name', '!=', $miName)
                    ->prepend([
                        'name' => $miName,
                        'code' => AP::getMiCode($miName),
                        'last_used_at' => now()->toDateTimeString(),
                    ])
                    ->sortByDesc('last_used_at')
                    ->take(20)
                    ->values(),
                AP::COOKIE_LIFETIME
            );
        }
    }
}
