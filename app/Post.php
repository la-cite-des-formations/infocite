<?php

namespace App;

use DateTime;
use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'icon', 'rubric_id', 'author_id', 'updated_by', 'published_at', 'expired_at'];
    protected $attributes = ['published' => FALSE, 'auto_delete' => FALSE];
    protected $casts = [
        'published_at' => 'nullable|date:Y-m-d',
        'expired_at' => 'nullable|date:Y-m-d',
    ];

    public function rubric()
    {
        return $this->belongsTo('App\Rubric');
    }

    public function corrector()
    {
        return $this->belongsTo('App\User', 'corrector_id');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function comments() {
        return $this
            ->hasMany('App\Comment')
            ->orderBy('created_at', 'DESC');
    }

    public function readers() {
        return $this
            ->belongsToMany('App\User')
            ->withPivot(['is_favorite', 'is_read', 'tags']);
    }

    public function notificableReaders() {
        return $this->rubric
            ->users
            ->merge($this
                ->readers()
                ->where('is_favorite', TRUE)
                ->get(['users.*'])
            );
    }

    public function notifications()
    {
        return $this
            ->hasMany('App\Notification')
            ->orderByRaw('release_at DESC, created_at DESC');
    }

    public function getRouteAttribute() {
        return $this->rubric->route()."/{$this->id}";
    }

    public function isCommentable() {
        return auth()->user()
            ->hasRole('comments', Roles::IS_EDITR, 'Post', $this->id);
    }

    public function isFavorite() {
        $postUser = $this->readers->find(auth()->user()->id);

        return $postUser ? $postUser->pivot->is_favorite : FALSE;
    }

    public function isRead() {
        $postUser = $this->readers->find(auth()->user()->id);

        return $postUser ? $postUser->pivot->is_read : FALSE;
    }

    public function tags() {
        $postUser = $this->readers->find(auth()->user()->id);

        return $postUser ? $postUser->pivot->tags : NULL;
    }

    public function getForthcomingAttribute() {
        return isset($this->published_at) && $this->published_at > today()->format('Y-m-d');
    }

    public function getExpiredAttribute() {
        return isset($this->expired_at) && $this->expired_at <= today()->format('Y-m-d');
    }

    public function getReleasedAttribute() {
        return $this->published && !$this->forthcoming && !$this->expired;
    }

    public function getArchivedAttribute() {
        return $this->published && $this->expired && !$this->auto_delete;
    }

    public function getStatusAttribute() {
        switch (TRUE) {
            case !$this->published : return AP::getPostStatusMI('unpublished');
            case $this->archived : return AP::getPostStatusMI('archived');
            case $this->expired : return AP::getPostStatusMI('expired');
            case $this->forthcoming : return AP::getPostStatusMI('forthcoming');
        }
    }

    public function preview() {
        return AP::strLimiter(strip_tags($this->content));
    }

    public function identity() {
        return "{$this->title} ({$this->rubric->name})";
    }

    public static function sort() {
        return self::query()
            ->orderByRaw('rubric_id ASC, title ASC')
            ->get();
    }

    public static function filter(array $filter) {
        extract($filter);

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
            });
    }
}
