<?php

namespace App\CustomFacades;

use Illuminate\Support\Facades\Facade;

/**
 * Façade pour la classe AP (Application Parameters).
 * Permet d'appeler les méthodes statiques de AP via un nom court 'AP'.
 */
class APFacade extends Facade
{
    /**
     * Récupère le nom enregistré du composant.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ap';
    }
}
