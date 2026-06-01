<?php

namespace App\Models;

use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente un commentaire sur un message (Post).
 */
class Comment extends Model
{
    use WithSearching;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['content', 'user_id'];

    /**
     * Relation vers le message auquel ce commentaire est associé.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post() {
        return $this
            ->belongsTo('App\Models\Post', 'post_id');
    }

    /**
     * Relation vers l'auteur du commentaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author() {
        return $this
            ->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Vérifie si le commentaire appartient à l'utilisateur authentifié.
     *
     * @return bool
     */
    public function isMine() {
        return auth()->user()->id == $this->user_id;
    }

    /**
     * Filtre les commentaires selon des critères (auteur, rubrique, message, recherche).
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $comments = static::query()
            ->when($authorId, function ($query) use ($authorId) {
                $query->where('user_id', $authorId);
            })
            ->when($rubricId, function ($query) use ($rubricId) {
                $query->whereIn('post_id', Rubric::find($rubricId)->posts->pluck('id'));
            })
            ->when($postId, function ($query) use ($postId) {
                $query->where('post_id', $postId);
            })
            ->get()
            ->when($search, function ($comments) use ($search) {
                return $comments->filter(function ($comment) use ($search) {
                    return static::tableContains([
                        $comment->content,
                        $comment->post->title,
                        $comment->author->identity,
                    ], $search);
                });
            });

        return $comments->isEmpty() ? static::whereNull('id') : $comments->toQuery();
    }
}
