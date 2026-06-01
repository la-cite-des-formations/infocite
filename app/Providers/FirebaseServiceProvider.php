<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;

/**
 * Fournisseur de services gérant l'intégration avec Firebase (via Kreait).
 * Permet l'envoi de notifications push et la gestion de la messagerie Firebase.
 */
class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(storage_path(config('firebase.credentials')));

            return $factory->createMessaging();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
