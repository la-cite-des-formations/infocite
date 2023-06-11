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
     * Determine whether the user can create apps for everybody.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('apps', Roles::IS_MODER);
    }

    /**
     * Determine whether the user can add his own apps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function add(User $user)
    {
        return $user->hasRole('apps', Roles::IS_EDITR);
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
        return $user->hasRole('apps', Roles::IS_MODER, 'App', $app->id);
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
        return $user->hasRole('apps', Roles::IS_MODER, 'App', $app->id);
    }

    /**
     * Determine whether the user can modify or remove his app.
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
