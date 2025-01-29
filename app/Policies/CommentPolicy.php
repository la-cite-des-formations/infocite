<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any comments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user, int $postId)
    {
        return $user->hasRole('comments', Roles::IS_READR, 'Post', $postId);
    }

    /**
     * Determine whether the user can view the comment.
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
     * Determine whether the user can create comments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, int $postId = NULL)
    {
        return $postId ?
            $user->hasRole('comments', Roles::IS_EDITR, 'Post', $postId) :
            $user->hasRole('comments', Roles::IS_EDITR);
    }

    /**
     * Determine whether the user can block comments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function block(User $user, int $postId = NULL)
    {
        return $postId ?
            $user->hasRole('comments', Roles::IS_MODER, 'Post', $postId) :
            $user->hasRole('comments', Roles::IS_MODER);
    }

    /**
     * Determine whether the user can delete any comments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasRole('comments', Roles::IS_MODER);
    }

    /**
     * Determine whether the user can delete the comment.
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
