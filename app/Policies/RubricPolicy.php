<?php

namespace App\Policies;

use App\CustomFacades\AP;
use App\Roles;
use App\Rubric;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class RubricPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the rubric (ui).
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function access(User $user, Rubric $rubric)
    {
        // vérification de l'accès à la rubrique concernée
        if ($user->myRubrics()->contains('id', $rubric->id)) {

            // vérification des droits d'accès de l'utilisateur ('Lecteur')
            // à la rubrique concernée
            if ($rubric->segment != "dashboard") {
                // la rubrique concernée n'est pas le tableau de bord
                return $user->hasRole('rubrics', Roles::IS_READR, 'Rubric', $rubric->id);
            }
            // traitement spécial pour le tableau de bord
            return
                $user->hasRole('rubrics', Roles::IS_READR, 'Rubric', $rubric->id, AP::STRICTLY) ??
                Gate::allows('access-dashboard');
        }

        return FALSE;
    }

    /**
     * Determine whether the user can view any rubrics.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR);
    }

    /**
     * Determine whether the user can view the rubric.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function view(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR, 'Rubric', $rubric->id);
    }

    /**
     * Determine whether the user can create rubrics.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR);
    }

    /**
     * Determine whether the user can update the rubric.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function update(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR, 'Rubric', $rubric->id);
    }

    /**
     * Determine whether the user can change the rubric segment.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function adminSegment(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_ADMIN, 'Rubric', $rubric->id);
    }

    /**
     * Determine whether the user can handle the rubric.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function handle(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_MODER, 'Rubric', $rubric->id);
    }

    /**
     * Determine whether the user can delete any rubrics.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('rubrics', Roles::IS_ADMIN);
    }

    /**
     * Determine whether the user can delete the rubric.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function delete(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_ADMIN, 'Rubric', $rubric->id);
    }
}
