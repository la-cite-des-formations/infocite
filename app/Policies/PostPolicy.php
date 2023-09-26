<?php

namespace App\Policies;

use App\CustomFacades\AP;
use App\Post;
use App\Roles;
use App\Rubric;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        // vérification de l'accès à la rubrique de l'arcticle concerné
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
     * Determine whether the user can edit posts in a specific rubric.
     *
     * @param  \App\User  $user
     * @param  int  $rubricId
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
     * Determine whether the user can create posts in a specific rubric.
     *
     * @param  \App\User  $user
     * @param  int  $rubricId
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
     * Determine whether the user can publish posts in a specific rubric.
     *
     * @param  \App\User  $user
     * @param  int  $rubricId
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
     * Determine whether the user can update the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        // vérification de l'accès à la rubrique de l'arcticle concerné
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
     * Determine whether the user can delete posts from a specific rubric.
     *
     * @param  \App\User  $user
     * @param  int  $rubricId
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
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        // vérification de l'accès à la rubrique de l'arcticle concerné
        if ($user->myRubrics()->contains('id', $post->rubric_id)) {

            // vérification des droits de l'utilisateur en suppression ('Administrateur')
            // sur l'article concerné
            $canDeletePost = $user->hasRole('posts', Roles::IS_ADMIN, 'Post', $post->id, AP::STRICTLY);

            return $canDeletePost ?? $this->clear($user, $post->rubric_id);
        }

        return FALSE;
    }
}
