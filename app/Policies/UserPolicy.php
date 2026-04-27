<?php

namespace App\Policies;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir la liste des utilisateurs ou des profils.
     *
     * @param  \App\Models\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function viewAny(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_READR);
    }

    /**
     * Détermine si l'utilisateur peut voir un utilisateur ou un profil spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $managedUser
     * @param  bool  $isProfile
     * @return mixed
     */
    public function view(User $user, User $managedUser, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_READR, 'User', $managedUser->id);
    }

    /**
     * Détermine si l'utilisateur peut créer de nouveaux utilisateurs ou profils.
     *
     * @param  \App\Models\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function create(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut modifier un utilisateur ou un profil spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $managedUser
     * @param  bool  $isProfile
     * @return mixed
     */
    public function update(User $user, User $managedUser, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_EDITR, 'User', $managedUser->id);
    }

    /**
     * Détermine si l'utilisateur peut supprimer n'importe quel utilisateur ou profil.
     *
     * @param  \App\Models\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function deleteAny(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_MODER);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un utilisateur ou un profil spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $managedUser
     * @param  bool  $isProfile
     * @return mixed
     */
    public function delete(User $user, User $managedUser, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_MODER, 'User', $managedUser->id);
    }

    /**
     * Détermine si l'utilisateur peut administrer les droits d'accès pour les utilisateurs ou les profils.
     *
     * @param  \App\Models\User  $user
     * @param  bool  $isProfile
     * @return mixed
     */
    public function adminRights(User $user, bool $isProfile = FALSE)
    {
        return $user->hasRole($isProfile ? 'profiles' : 'users', Roles::IS_ADMIN);
    }
}
