<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Channels\FcmChannel;
use App\Models\Post;
use App\Observers\PostObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
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
