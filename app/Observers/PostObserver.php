<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function deleting(Post $post)
    {
        foreach ($post->notifications as $notification) {
            $notification->users()->detach();
            $notification->delete();
        }
    }
}
