<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function deleting(Post $post)
    {
        if (method_exists($post, 'isForceDeleting') && $post->isForceDeleting()) {
            $post->notifications()->forceDelete();
        } else {
            $post->notifications()->delete();
        }
    }
}
