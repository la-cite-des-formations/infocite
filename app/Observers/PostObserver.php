<?php

namespace App\Observers;

use App\Models\Post;

/**
 * Observateur pour le modèle Post.
 * Gère les actions automatiques lors de la modification ou suppression d'un article.
 */
class PostObserver
{
    /**
     * Action déclenchée avant la suppression d'un article.
     * Nettoie les notifications associées et détache les liens utilisateurs.
     *
     * @param Post $post L'instance de l'article en cours de suppression.
     * @return void
     */
    public function deleting(Post $post)
    {
        foreach ($post->notifications as $notification) {
            $notification->users()->detach();
            $notification->delete();
        }
    }
}
