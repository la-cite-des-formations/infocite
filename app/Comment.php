<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
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

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('content', 'like', "%$search%");
            })
            ->when($authorId, function ($query) use ($authorId) {
                $query->where('user_id', $authorId);
            })
            ->when($rubricId, function ($query) use ($rubricId) {
                $query->whereIn('post_id', Rubric::find($rubricId)->posts->pluck('id'));
            })
            ->when($postId, function ($query) use ($postId) {
                $query->where('post_id', $postId);
            });
    }
}
