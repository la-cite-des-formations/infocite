<?php

namespace App\Policies;

use App\CustomFacades\AP;
use App\Models\Post;
use App\Models\Roles;
use App\Models\Rubric;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Politique d'accès pour le modèle Post (Articles).
 * Gère les permissions complexes basées sur les rubriques, l'état de publication et les rôles.
 */
class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut lire un article.
     * Vérifie l'accès à la rubrique et si l'article est publié ou éditable par l'utilisateur.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function read(User $user, Post $post)
    {
        // vérification de l'accès à la rubrique de l'article concerné
        if ($user->myRubrics()->contains('id', $post->rubric_id)) {

            // vérification des droits de l'utilisateur en lecture ('Lecteur')
            // sur l'article concerné (si publié ou si éditable par l'utilisateur)
            return
                ($post->published || $this->update($user, $post)) &&
                $user->hasRole('posts', Roles::IS_READR, 'Post', $post->id);
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut créer des articles dans une rubrique spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  int|null $rubricId ID de la rubrique.
     * @return mixed
     */
    public function create(User $user, int $rubricId = NULL)
    {
        // vérification de l'accès à la rubrique concernée
        if ($user->myRubrics()->contains('id', $rubricId) || is_null($rubricId)) {

            // vérification des droits de l'utilisateur en édition ('Editeur')
            // sur les articles spécifiques à la rubrique concernée
            $rubric = is_null($rubricId) ? NULL : Rubric::find($rubricId);
            $rootRubric = is_null($rubric) ? NULL : $rubric->parent ?? $rubric;
            $canCreatePosts = $user->hasRole('posts', Roles::IS_EDITR, 'Rubric', $rubricId, AP::STRICTLY);

            return $canCreatePosts ?? $user->hasRole(
                'posts',
                Roles::IS_EDITR,
                'Rubric',
                is_null($rootRubric) ? NULL : $rootRubric->id
            );
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut modifier les articles d'une rubrique.
     * Utilisé pour la gestion en masse ou l'accès aux interfaces d'édition.
     *
     * @param  \App\Models\User  $user
     * @param  int  $rubricId ID de la rubrique.
     * @return mixed
     */
    public function edit(User $user, int $rubricId)
    {
        // vérification de l'accès à la rubrique concernée
        if ($user->myRubrics()->contains('id', $rubricId) || is_null($rubricId)) {
            if ((Rubric::find($rubricId))->name == 'Une') {
                $rightableType = NULL;
                $rightableId = NULL;
            }
            else {
                $rightableType = 'Rubric';
                $rightableId = $rubricId;
            }

            return
                $user->hasRole('posts', Roles::IS_EDITR, $rightableType, $rightableId) ||
                $user->hasRole('posts', Roles::IS_MODER, $rightableType, $rightableId) ||
                $user->hasRole('posts', Roles::IS_ADMIN, $rightableType, $rightableId);
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut publier des articles dans une rubrique.
     * Nécessite le rôle 'Modérateur' sur la rubrique concernée.
     *
     * @param  \App\Models\User  $user
     * @param  int|null $rubricId ID de la rubrique.
     * @return mixed
     */
    public function publish(User $user, int $rubricId = NULL)
    {
        // vérification de l'accès à la rubrique concernée
        if ($user->myRubrics()->contains('id', $rubricId) || is_null($rubricId)) {

            // vérification des droits de l'utilisateur en publication ('Modérateur')
            // sur la rubrique concernée
            $rubric = is_null($rubricId) ? NULL : Rubric::find($rubricId);
            $rootRubric = is_null($rubric) ? NULL : $rubric->parent ?? $rubric;
            $canPublishPosts = $user->hasRole('posts', Roles::IS_MODER, 'Rubric', $rubricId, AP::STRICTLY);

            return $canPublishPosts ?? $user->hasRole(
                'posts',
                Roles::IS_MODER,
                'Rubric',
                is_null($rootRubric) ? NULL : $rootRubric->id
            );
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un article spécifique.
     * Vérifie si l'article est déjà publié (nécessite Modérateur) ou en brouillon (Editeur suffit).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        // vérification de l'accès à la rubrique de l'article concerné
        if ($user->myRubrics()->contains('id', $post->rubric_id)) {

            // vérification des droits de l'utilisateur en édition ('Editeur' / 'Modérateur')
            // sur l'article concerné
            if ($post->released) {
                $canUpdatePost = $user->hasRole('posts', Roles::IS_EDITR + Roles::IS_MODER, 'Post', $post->id, AP::STRICTLY);

                return $canUpdatePost ??
                    $this->create($user, $post->rubric_id) &&
                    $this->publish($user, $post->rubric_id);
            }
            else {
                $canUpdatePost = $user->hasRole('posts', Roles::IS_EDITR, 'Post', $post->id, AP::STRICTLY);

                return $canUpdatePost ?? $this->create($user, $post->rubric_id);
            }
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut supprimer tous les articles d'une rubrique.
     * Nécessite le rôle 'Administrateur' sur la rubrique.
     *
     * @param  \App\Models\User  $user
     * @param  int  $rubricId ID de la rubrique.
     * @return mixed
     */
    public function clear(User $user, int $rubricId) {
        // vérification de l'accès à la rubrique concernée
        if ($user->myRubrics()->contains('id', $rubricId) || is_null($rubricId)) {

            // vérification des droits de l'utilisateur en suppression ('Administrateur')
            // sur les articles spécifiques à la rubrique concernée
            $rubric = is_null($rubricId) ? NULL : Rubric::find($rubricId);
            $rootRubric = is_null($rubric) ? NULL : $rubric->parent ?? $rubric;
            $canDeletePosts = $user->hasRole('posts', Roles::IS_ADMIN, 'Rubric', $rubricId, AP::STRICTLY);

            return $canDeletePosts ?? $user->hasRole(
                'posts',
                Roles::IS_ADMIN,
                'Rubric',
                is_null($rootRubric) ? NULL : $rootRubric->id
            );
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un article spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        // vérification de l'accès à la rubrique de l'article concerné
        if ($user->myRubrics()->contains('id', $post->rubric_id)) {

            // vérification des droits de l'utilisateur en suppression ('Administrateur')
            // sur l'article concerné
            $canDeletePost = $user->hasRole('posts', Roles::IS_ADMIN, 'Post', $post->id, AP::STRICTLY);

            return $canDeletePost ?? $this->clear($user, $post->rubric_id);
        }

        return FALSE;
    }

    /**
     * Détermine si l'utilisateur peut épingler un article (mise à la Une).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function pin(User $user, Post $post)
    {
        return $user->hasRole('posts', Roles::IS_MODER) && $post->released;
    }
}
