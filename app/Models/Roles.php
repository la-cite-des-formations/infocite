<?php

namespace App\Models;

use Illuminate\Support\Collection;

/**
 * Classe utilitaire définissant les rôles (flags binaires) au sein du portail.
 * Gère la conversion entre flags et libellés humains.
 */
class Roles
{
    /** @var int Aucun droit (0). */
    const NONE =     0b0000;
    /** @var int Droit d'administration (1). */
    const IS_ADMIN = 0b0001;
    /** @var int Droit de modération (2). */
    const IS_MODER = 0b0010;
    /** @var int Droit d'édition (4). */
    const IS_EDITR = 0b0100;
    /** @var int Droit de lecture simple (8). */
    const IS_READR = 0b1000;
    /** @var int Tous les droits combinés (15). */
    const ALL =      0b1111;

    const ATLEAST_FILTER = 'atleast';
    const EXACTLY_FILTER = 'exactly';

    const NONE_STRING = 'Aucun droit';

    /** @var array Liste exhaustive des rôles avec leurs métadonnées. */
    private static $roles = [
        [
            'id' => 'reader',
            'name' => 'Lecteur',
            'placeholder' => "Rôle du lecteur",
            'title' => 'rd_role',
            'description' => 'rd_description',
            'flag' => self::IS_READR,
        ],
        [
            'id' => 'editor',
            'name' => 'Éditeur',
            'placeholder' => "Rôle de l'éditeur",
            'title' => 'ed_role',
            'description' => 'ed_description',
            'flag' => self::IS_EDITR,
        ],
        [
            'id' => 'moderator',
            'name' => 'Modérateur',
            'placeholder' => "Rôle du modérateur",
            'title' => 'md_role',
            'description' => 'md_description',
            'flag' => self::IS_MODER,
        ],
        [
            'id' => 'admin',
            'name' => 'Administrateur',
            'placeholder' => "Rôle de l'admin",
            'title' => 'ad_role',
            'description' => 'ad_description',
            'flag' => self::IS_ADMIN,
        ],
    ];

    /**
     * Retourne la collection complète des rôles sous forme d'objets.
     *
     * @return object
     */
    public static function all()
    {
        return (object)[
            'class' => static::class,
            'collection' => new Collection(array_map(function ($role) { return (object) $role; }, static::$roles))
        ];
    }

    /**
     * Filtre les rôles en fonction d'un masque binaire.
     *
     * @param int $rolesFlag Masque binaire des rôles désirés.
     * @return \Illuminate\Support\Collection
     */
    public static function filter($rolesFlag) {
        return static::all()
            ->collection
            ->filter(function ($role) use ($rolesFlag) { return $rolesFlag & $role->flag; });
    }
}
