<?php

namespace App\Policies;

use App\Right;
use App\Roles;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RightPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the current user can view any rights.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('rights', Roles::IS_READR);
    }

    /**
     * Determine whether the current user can view the right.
     *
     * @param  \App\User  $user
     * @param  \App\Right  $right
     * @return mixed
     */
    public function view(User $user, Right $right)
    {
        return $user->hasRole('rights', Roles::IS_READR, 'Right', $right->id);
    }

    /**
     * Determine whether the current user can create any rights.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('rights', Roles::IS_EDITR);
    }

    /**
     * Determine whether the current user can update the right.
     *
     * @param  \App\User  $user
     * @param  \App\Right  $right
     * @return mixed
     */
    public function update(User $user, Right $right)
    {
        return $user->hasRole('rights', Roles::IS_MODER, 'Right', $right->id) ;
    }

    /**
     * Determine whether the current user can delete any managed users or profiles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('rights', Roles::IS_ADMIN);
    }

    /**
     * Determine whether the current user can delete the managed user or the profile.
     *
     * @param  \App\User  $user
     * @param  \App\Right  $right
     * @return mixed
     */
    public function delete(User $user, Right $right)
    {
        return $user->hasRole('rights', Roles::IS_ADMIN, 'Right', $right->id);
    }
}
