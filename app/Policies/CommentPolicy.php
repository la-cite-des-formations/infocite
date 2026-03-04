<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Politique d'accès pour le modèle Comment.
 * Gère les permissions pour visualiser, bloquer et supprimer les commentaires.
 */
class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir les commentaires d'un article spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  int $postId ID de l'article.
     * @return mixed
     */
    public function viewAny(User $user, int $postId)
    {
        return $user->hasRole('comments', Roles::IS_READR, 'Post', $postId);
    }

    /**
     * Détermine si l'utilisateur peut voir un commentaire spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return mixed
     */
    public function view(User $user, Comment $comment)
    {
        return $user->hasRole('comments', Roles::IS_READR, 'Post', $comment->post->id);
    }

    /**
     * Détermine si l'utilisateur peut créer des commentaires sur un article.
     *
     * @param  \App\Models\User  $user
     * @param  int|null $postId ID de l'article (optionnel).
     * @return mixed
     */
    public function create(User $user, int $postId = NULL)
    {
        return $postId ?
            $user->hasRole('comments', Roles::IS_EDITR, 'Post', $postId) :
            $user->hasRole('comments', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut masquer/bloquer des commentaires.
     *
     * @param  \App\Models\User  $user
     * @param  int|null $postId ID de l'article (optionnel).
     * @return mixed
     */
    public function block(User $user, int $postId = NULL)
    {
        return $postId ?
            $user->hasRole('comments', Roles::IS_MODER, 'Post', $postId) :
            $user->hasRole('comments', Roles::IS_MODER);
    }

    /**
     * Détermine si l'utilisateur peut supprimer n'importe quel commentaire (modération globale).
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('comments', Roles::IS_MODER);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un commentaire spécifique.
     * L'auteur peut supprimer son propre commentaire, ou un modérateur peut supprimer n'importe lequel.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Comment  $comment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        return
            $user->hasRole('comments', Roles::IS_EDITR, 'Post', $comment->post->id) && $comment->isMine() ||
            $user->hasRole('comments', Roles::IS_MODER, 'Post', $comment->post->id);
    }
}
