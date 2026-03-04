<?php

namespace App\Policies;

use App\Models\Right;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Politique d'accès pour le modèle Right (Droits).
 * Gère les permissions pour visualiser, créer, modifier et supprimer les paramétrages de droits.
 */
class RightPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir la liste des droits.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('rights', Roles::IS_READR);
    }

    /**
     * Détermine si l'utilisateur peut voir un paramétrage de droit spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Right  $right
     * @return mixed
     */
    public function view(User $user, Right $right)
    {
        return $user->hasRole('rights', Roles::IS_READR, 'Right', $right->id);
    }

    /**
     * Détermine si l'utilisateur peut créer de nouveaux droits.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('rights', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut modifier un droit spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Right  $right
     * @return mixed
     */
    public function update(User $user, Right $right)
    {
        return $user->hasRole('rights', Roles::IS_MODER, 'Right', $right->id) ;
    }

    /**
     * Détermine si l'utilisateur peut supprimer n'importe quel droit.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('rights', Roles::IS_ADMIN);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un droit spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Right  $right
     * @return mixed
     */
    public function delete(User $user, Right $right)
    {
        return $user->hasRole('rights', Roles::IS_ADMIN, 'Right', $right->id);
    }
}
