<?php

namespace App\Policies;

use App\Models\Roles;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Politique d'accès pour le modèle Group.
 * Gère les permissions pour visualiser, créer, modifier et supprimer des groupes d'utilisateurs.
 */
class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir la liste des groupes.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('groups', Roles::IS_READR);
    }

    /**
     * Détermine si l'utilisateur peut voir un groupe spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_READR, 'Group', $group->id);
    }

    /**
     * Détermine si l'utilisateur peut créer des groupes.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('groups', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut modifier un groupe spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_EDITR, 'Group', $group->id);
    }

    /**
     * Détermine si l'utilisateur peut gérer un groupe (actions administratives avancées).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function handle(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_MODER, 'Group', $group->id);
    }

    /**
     * Détermine si l'utilisateur peut supprimer n'importe quel groupe.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('groups', Roles::IS_ADMIN);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un groupe spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $user->hasRole('groups', Roles::IS_ADMIN, 'Group', $group->id);
    }
}
