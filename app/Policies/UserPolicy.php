<?php

namespace App\Policies;

use App\Roles;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the current user can view any managed users or profiles.
     *
     * @param  \App\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function viewAny(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_READR);
    }

    /**
     * Determine whether the current user can view the managed user or the profile.
     *
     * @param  \App\User  $user
     * @param  \App\User  $managedUser
     * @param  bool  $isProfile
     * @return mixed
     */
    public function view(User $user, User $managedUser, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_READR, 'User', $managedUser->id);
    }

    /**
     * Determine whether the current user can create any managed users or profiles.
     *
     * @param  \App\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function create(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_EDITR);
    }

    /**
     * Determine whether the current user can edit the managed user or the profile.
     *
     * @param  \App\User  $user
     * @param  \App\User  $managedUser
     * @param  bool  $isProfile
     * @return mixed
     */
    public function update(User $user, User $managedUser, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_EDITR, 'User', $managedUser->id);
    }

    /**
     * Determine whether the current user can delete any managed users or profiles.
     *
     * @param  \App\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function deleteAny(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_MODER);
    }

    /**
     * Determine whether the current user can delete the managed user or the profile.
     *
     * @param  \App\User  $user
     * @param  \App\User  $managedUser
     * @param  bool  $isProfile
     * @return mixed
     */
    public function delete(User $user, User $managedUser, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_MODER, 'User', $managedUser->id);
    }

    /**
     * Determine whether the current user can admin rights for any managed users or profiles.
     *
     * @param  \App\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function adminRights(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_ADMIN);
    }
}
