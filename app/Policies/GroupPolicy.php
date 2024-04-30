<?php

namespace App\Policies;

use App\Roles;
use App\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('groups', Roles::IS_READR);
    }

    /**
     * Determine whether the user can view the group.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_READR, 'Group', $group->id);
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('groups', Roles::IS_EDITR);
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_EDITR, 'Group', $group->id);
    }

    /**
     * Determine whether the user can handle the group.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function handle(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_MODER, 'Group', $group->id);
    }

    /**
     * Determine whether the current user can delete any groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('groups', Roles::IS_ADMIN);
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_ADMIN, 'Group', $group->id);
    }
}
