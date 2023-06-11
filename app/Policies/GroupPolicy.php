<?php

namespace App\Policies;

use App\CustomFacades\AP;
use App\Roles;
use App\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the rubric.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $rubric
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine si l'utilisateur peut créer un groupe de type 'P' (processus) - gestion de l'organigramme
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function createProcessGroup(User $user)
    {
        return $user->hasRole('org-chart', Roles::IS_MODER);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
        //
    }
}
