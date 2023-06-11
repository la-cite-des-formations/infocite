<?php

namespace App;

use Illuminate\Support\Collection;

class Roles
{
    const NONE =     0b0000;
    const IS_ADMIN = 0b0001;
    const IS_MODER = 0b0010;
    const IS_EDITR = 0b0100;
    const IS_READR = 0b1000;
    const ALL =      0b1111;

    const ATLEAST_FILTER = 'atleast';
    const EXACTLY_FILTER = 'exactly';

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

    public static function users(int $rolesFlag, string $rolesOption, int $systemGroupID)
    {
        $users = Group::find($systemGroupID)->users();

        switch ($rolesOption) {
            case static::ATLEAST_FILTER :
                $atleastRolesFlag = [];
                for ($rf = static::NONE; $rf <= static::ALL; $rf++) {
                    if (($rf & $rolesFlag) === $rolesFlag) {
                        $atleastRolesFlag[] = sprintf('%04b', $rf);
                    }
                }
            return $users->wherePivotIn('function', $atleastRolesFlag);

            case static::EXACTLY_FILTER :
            return $users->wherePivot('function', sprintf('%04b', $rolesFlag));
        }
    }

    public static function all()
    {
        return (object)[
            'class' => static::class,
            'collection' => new Collection(array_map(function ($role) { return (object) $role; }, static::$roles))
        ];
    }

    public static function filter($rolesFlag) {
        return static::all()
            ->collection
            ->filter(function ($role) use ($rolesFlag) { return $rolesFlag & $role->flag; });
    }
}
