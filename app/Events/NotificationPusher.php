<?php

namespace App\Events;

use App\Notification;
use App\Post;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationPusher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $notification;
    public $post;
    public $userIds;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Notification $notification, array $userIds)
    {
        $this->notification = $notification;
        $this->post = Post::find($this->notification->post_id);
        $this->userIds = $userIds;
    }


    public function broadcastWith()
    {
        return [
            'message'=>$this->notification->message,
            'post'=>$this->post->title,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): Channel|PrivateChannel|array
    {
        return collect($this->userIds)->map(function ($id) {
            return new Channel('notificationPostChannel.' . $id);
        })->toArray();

    }


}
