<?php

namespace App\Services;

use App\Models\Notification as PostNotification;
use App\Models\Post;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Support\Facades\Notification;

class PostNotificationService
{
    /**
     * Gère les notifications (cloche + Firebase) lors de la création ou modification d'un article.
     *
     * @param Post $post
     * @param User|null $triggerUser
     * @return void
     */
    public function notifyPostSave(Post $post, ?User $triggerUser = null): void
    {
        if (!$post->released) {
            return;
        }

        // 1. Notification interne (cloche)
        $newPostNotification = PostNotification::query()
            ->where('content_type', 'NP')
            ->where('object_type', Post::class)
            ->where('object_id', $post->id);

        if ($newPostNotification->exists()) {
            $newPostNotification->update(['release_at' => $post->published_at]);

            $postNotification = PostNotification::updateOrCreate(
                ['content_type' => 'UP', 'object_type' => Post::class, 'object_id' => $post->id],
                ['release_at' => $post->updated_at]
            );
        } else {
            $postNotification = PostNotification::create([
                'content_type' => 'NP',
                'object_type'  => Post::class,
                'object_id'    => $post->id,
                'release_at'   => $post->published_at,
            ]);
        }

        $postNotification->users()->syncWithoutDetaching($post->notificableReaders()->pluck('id'));

        // 2. Notification Push Firebase
        $this->sendDesktopNotification($post, $postNotification->content_type, $triggerUser);
    }

    /**
     * Gère les notifications (cloche + Firebase) lors du dépôt d'un commentaire.
     *
     * @param Post $post
     * @param User|null $triggerUser
     * @return void
     */
    public function notifyComment(Post $post, ?User $triggerUser = null): void
    {
        if (!$post->released) {
            return;
        }

        // 1. Notification interne (cloche)
        $commentNotification = PostNotification::updateOrCreate(
            ['content_type' => 'CP', 'object_type' => Post::class, 'object_id' => $post->id],
            ['release_at' => today()->format('Y-m-d')]
        );

        $commentNotification->users()->syncWithoutDetaching($post->notificableReaders()->pluck('id'));

        // 2. Notification Push Firebase
        $this->sendDesktopNotification($post, 'CP', $triggerUser);
    }

    /**
     * Envoie la notification de bureau Firebase aux destinataires qualifiés.
     *
     * @param Post $post
     * @param string $contentType ('NP', 'UP', 'CP')
     * @param User|null $triggerUser
     * @return void
     */
    protected function sendDesktopNotification(Post $post, string $contentType, ?User $triggerUser = null): void
    {
        $targetUsers = $post->notificableDesktopUsers($triggerUser);

        if ($targetUsers->isNotEmpty()) {
            Notification::send($targetUsers, new AppNotification([
                'type' => $contentType,
                'post' => $post,
            ]));
        }
    }
}
