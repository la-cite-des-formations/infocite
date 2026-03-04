<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Fournisseur de services personnalisé pour les composants spécifiques au portail.
 * Gère notamment l'enregistrement de la façade 'AP' pour l'accès aux constantes et utilitaires.
 */
class CustomServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les liaisons de services personnalisées dans le conteneur.
     * Lie le nom 'ap' à l'instance de la classe Application Parameters.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ap', function () {
            return new \App\CustomFacades\AP;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
