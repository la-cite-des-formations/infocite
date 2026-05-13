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
        $notificationIds = $post->notifications()->pluck('id');
        if ($notificationIds->isNotEmpty()) {
            \Illuminate\Support\Facades\DB::table('notification_user')
                ->whereIn('notification_id', $notificationIds)
                ->delete();
            \App\Models\Notification::whereIn('id', $notificationIds)->delete();
        }

        // Nettoyage des interactions liées (polymorphique)
        \App\Models\Interaction::where('target_type', 'App\Models\Post')
            ->where('target_id', $post->id)
            ->delete();

        // Nettoyage des favoris liés (polymorphique)
        \Illuminate\Support\Facades\DB::table('favorites')
            ->where('favoriteable_type', 'App\Models\Post')
            ->where('favoriteable_id', $post->id)
            ->delete();

        // Nettoyage des lecteurs (pivot post_user)
        $post->readers()->detach();
    }
}
