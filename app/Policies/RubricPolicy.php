<?php

namespace App\Policies;

use App\CustomFacades\AP;
use App\Models\Roles;
use App\Models\Rubric;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

/**
 * Politique d'accès pour le modèle Rubric (Rubriques).
 * Gère l'accès public aux rubriques et leur administration technique.
 */
class RubricPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut accéder au contenu d'une rubrique (UI).
     * Vérifie l'accès via les droits personnalisés 'rubrics'.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rubric  $rubric
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
     * Détermine si l'utilisateur peut voir la liste des rubriques dans l'administration.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut voir une rubrique spécifique dans l'administration.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rubric  $rubric
     * @return mixed
     */
    public function view(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR, 'Rubric', $rubric->id);
    }

    /**
     * Détermine si l'utilisateur peut créer des rubriques.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut modifier une rubrique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rubric  $rubric
     * @return mixed
     */
    public function update(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_EDITR, 'Rubric', $rubric->id);
    }

    /**
     * Détermine si l'utilisateur peut modifier le segment technique d'une rubrique.
     * Nécessite le rôle 'Administrateur' sur la rubrique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rubric  $rubric
     * @return mixed
     */
    public function adminSegment(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_ADMIN, 'Rubric', $rubric->id);
    }

    /**
     * Détermine si l'utilisateur peut gérer une rubrique (actions de modération).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rubric  $rubric
     * @return mixed
     */
    public function handle(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_MODER, 'Rubric', $rubric->id);
    }

    /**
     * Détermine si l'utilisateur peut supprimer n'importe quelle rubrique.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('rubrics', Roles::IS_ADMIN);
    }

    /**
     * Détermine si l'utilisateur peut supprimer une rubrique spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rubric  $rubric
     * @return mixed
     */
    public function delete(User $user, Rubric $rubric)
    {
        return $user->hasRole('rubrics', Roles::IS_ADMIN, 'Rubric', $rubric->id);
    }
}
