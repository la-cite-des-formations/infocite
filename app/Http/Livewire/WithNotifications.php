<?php

namespace App\Http\Livewire;

trait WithNotifications
{
    public $notifications;

    protected $closedModalCallback = ['detachNotifications', 'setNotifications'];

    public function setNotifications() {
        $this->notifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', today());
    }

    public function detachNotifications() {
        $notificationsIds = $this->notifications->pluck('id');

        auth()->user()->newNotifications()->detach($notificationsIds);
    }
}
