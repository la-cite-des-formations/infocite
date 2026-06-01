<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Channels\FcmChannel;
use App\Models\Post;
use App\Observers\PostObserver;

/**
 * Fournisseur de services principal de l'application.
 * Gère l'enregistrement des observateurs, le mapping polymorphique et les extensions système.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les services de l'application dans le conteneur de services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Initialise les services de l'application au démarrage (Bootstrap).
     * Configure les observateurs de modèles, les relations polymorphiques
     * et étend le gestionnaire de notifications par défaut pour inclure FCM.
     *
     * @return void
     */
    public function boot()
    {
        Post::observe(PostObserver::class);

        Relation::morphMap([
            'Group' => 'App\Models\Group',
            'User' => 'App\Models\User',
        ]);

        $this->app->make(ChannelManager::class)->extend('fcm', function ($app) {
            // Demande au conteneur de construire FcmChannel en injectant ses dépendances
            return $app->make(FcmChannel::class);
        });
    }
}
