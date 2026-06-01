<?php

namespace App\Policies;

use App\Models\App;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Politique d'accès pour le modèle App.
 * Gère les permissions pour visualiser, créer, modifier et supprimer des applications.
 */
class AppPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir la liste des applications.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('apps', Roles::IS_READR);
    }

    /**
     * Détermine si l'utilisateur peut voir une application spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return mixed
     */
    public function view(User $user, App $app)
    {
        return auth()->user()->myApps()->contains('id', $app->id) && $user->hasRole('apps', Roles::IS_READR, 'App', $app->id);
    }

    /**
     * Détermine si l'utilisateur peut filtrer les applications par type.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function filterByType(User $user)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN);
    }

    /**
     * Détermine si l'utilisateur peut créer des applications institutionnelles.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('apps', Roles::IS_MODER);
    }

    /**
     * Détermine si l'utilisateur peut créer des applications personnelles pour un tiers.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function createFor(User $user)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN);
    }

    /**
     * Détermine si l'utilisateur peut modifier une application (institutionnelle).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return mixed
     */
    public function update(User $user, App $app)
    {
        return $app->isInstitutional() && $user->hasRole('apps', Roles::IS_MODER, 'App', $app->id);
    }

    /**
     * Détermine si l'utilisateur peut modifier une application personnelle pour un tiers.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return mixed
     */
    public function updateFor(User $user, App $app)
    {
        return $app->isPersonal() && $user->hasRole('apps', Roles::IS_ADMIN, 'App', $app->id);
    }

    /**
     * Détermine si l'utilisateur peut supprimer n'importe quelle application.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN);
    }

    /**
     * Détermine si l'utilisateur peut supprimer une application spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return mixed
     */
    public function delete(User $user, App $app)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN, 'App', $app->id);
    }

    /**
     * Détermine si l'utilisateur peut ajouter ses propres applications (usage front/personnel).
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function add(User $user)
    {
        return $user->hasRole('apps', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut modifier ou supprimer sa propre application (usage front).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return mixed
     */
    public function handle(User $user, App $app)
    {
        return $app->isMine() && $user->hasRole('apps', Roles::IS_EDITR);
    }
}
