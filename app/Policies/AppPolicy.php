<?php

namespace App\Policies;

use App\App;
use App\Roles;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any apps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('apps', Roles::IS_READR);
    }

    /**
     * Determine whether the user can view the app.
     *
     * @param  \App\User  $user
     * @param  \App\App  $app
     * @return mixed
     */
    public function view(User $user, App $app)
    {
        return $user->hasRole('apps', Roles::IS_READR, 'App', $app->id);
    }

    /**
     * Determine whether the user can filter apps by type.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function filterByType(User $user)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN);
    }

    /**
     * Determine whether the user can create institutional apps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('apps', Roles::IS_MODER);
    }

    /**
     * Determine whether the user can create personal apps for someone.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function createFor(User $user)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN);
    }

    /**
     * Determine whether the user can update the app.
     *
     * @param  \App\User  $user
     * @param  \App\App  $app
     * @return mixed
     */
    public function update(User $user, App $app)
    {
        return $app->isInstitutional() && $user->hasRole('apps', Roles::IS_MODER, 'App', $app->id);
    }

    /**
     * Determine whether the user can update the personal app for someone.
     *
     * @param  \App\User  $user
     * @param  \App\App  $app
     * @return mixed
     */
    public function updateFor(User $user, App $app)
    {
        return $app->isPersonal() && $user->hasRole('apps', Roles::IS_ADMIN, 'App', $app->id);
    }

    /**
     * Determine whether the user can delete any apps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN);
    }

    /**
     * Determine whether the user can delete the app.
     *
     * @param  \App\User  $user
     * @param  \App\App  $app
     * @return mixed
     */
    public function delete(User $user, App $app)
    {
        return $user->hasRole('apps', Roles::IS_ADMIN, 'App', $app->id);
    }

    /**
     * Determine whether the user can add his own apps (usage front).
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function add(User $user)
    {
        return $user->hasRole('apps', Roles::IS_EDITR);
    }

    /**
     * Determine whether the user can update or delete his app (usage front).
     *
     * @param  \App\User  $user
     * @param  \App\App  $app
     * @return mixed
     */
    public function handle(User $user, App $app)
    {
        return $app->isMine() && $user->hasRole('apps', Roles::IS_EDITR);
    }
}
