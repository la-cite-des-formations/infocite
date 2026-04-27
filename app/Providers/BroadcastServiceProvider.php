<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Fournisseur de services gérant la diffusion d'événements (Broadcasting).
 * Permet de partager des événements entre le serveur et le client en temps réel.
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Initialise les services de diffusion au démarrage de l'application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
