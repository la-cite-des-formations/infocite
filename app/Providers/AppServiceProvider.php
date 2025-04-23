<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Channels\FcmChannel;

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
        Relation::morphMap([
            'Group' => 'App\Models\Group',
            'User' => 'App\Models\User',
        ]);

        session([
            'displayPosts'=>'grid',
            'lastFilter'=>'allPosts',
        ]);

        $this->app->make(ChannelManager::class)->extend('firebase', function () {
            return new FcmChannel;
        });
    }
}
