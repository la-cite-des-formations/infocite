<?php

namespace App;

use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use WithSearching;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'user_id'];

    public function post() {
        return $this
            ->belongsTo('App\Post', 'post_id');
    }

    public function author() {
        return $this
            ->belongsTo('App\User', 'user_id');
    }

    public function isMine() {
        return auth()->user()->id == $this->user_id;
    }

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
