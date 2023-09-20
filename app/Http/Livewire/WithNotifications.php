<?php

namespace App\Http\Livewire;

trait WithNotifications
{
    public $notifications;

    protected $closedModalCallback = ['updateNotifications', 'setNotifications'];

    public function setNotifications() {
        $this->notifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', today());
    }

    public function updateNotifications() {
        $notificationsIds = $this->notifications->pluck('id');

        auth()->user()->newNotifications()->detach($notificationsIds);
    }
}
