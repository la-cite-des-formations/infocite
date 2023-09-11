<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;

class NotificationsManager extends Component
{
    public $nbMaxNotif = 20;

    public function consumedNotif($notificationId) {
        auth()->user()->newNotifications()->detach($notificationId);
    }

    public function render()
    {
        $newNotifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', today())
            ->sortByDesc('release_at')
            ->sortByDesc('created_at');

        $nbMaxOldNotif = $this->nbMaxNotif > $newNotifications->count() ?
            $this->nbMaxNotif - $newNotifications->count() : 0;
        return view('livewire.modals.usage.notifications-manager', [
            'newNotifications' => $newNotifications,
            'oldNotifications' => auth()->user()
                ->oldNotifications()
                ->sortByDesc('release_at')
                ->sortByDesc('created_at')
                ->take($nbMaxOldNotif),
        ]);
    }
}
