<?php

namespace App\Http\Livewire;

use App\CustomFacades\AP;
use DateTime;
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

    public function choiceIcon($miName, string $model) {
        if(isset($this->$model)) {
            $this->$model->icon = $miName;
            Cookie::queue(
                'recentMiCodes',
                AP::getRecentMiCodes()
                    ->merge([$miName => [
                        'code' => AP::getMiCode($miName),
                        'created_at' => new DateTime()
                    ]])
                    ->sortByDesc('created_at')
                    ->take(20),
                AP::COOKIE_LIFETIME
            );
            // Cookie::queue('recentMiCodes', Cookie::forget('recentMicodes'));
        }
    }
}
