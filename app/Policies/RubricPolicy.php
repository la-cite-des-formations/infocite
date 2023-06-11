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
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function view(User $user, Rubric $rubric)
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
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rubric  $rubric
     * @return mixed
     */
    public function update(User $user, Rubric $rubric)
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
    public function delete(User $user, Rubric $rubric)
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
    public function restore(User $user, Rubric $rubric)
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
    public function forceDelete(User $user, Rubric $rubric)
    {
        //
    }
}
