<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;

class NotificationsManager extends Component
{
    public $perPage = 8;

    public function render()
    {
        return view('livewire.modals.usage.notifications-manager', [
            'notifications' => auth()->user()
                ->myNotifications()
                ->sortBy('consulted')
                ->sortByDesc('created_at')
        ]);
    }
}
