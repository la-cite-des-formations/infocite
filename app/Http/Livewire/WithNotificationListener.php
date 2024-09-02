<?php

namespace App\Http\Livewire;

trait WithNotificationListener
{
    public function pushedNotification() {
        $this->setNotifications();
    }
}
