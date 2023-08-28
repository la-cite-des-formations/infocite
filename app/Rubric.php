<?php

namespace App;

use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'icon', 'is_parent', 'parent_id', 'position', 'rank', 'contains_posts', 'segment'];

    public function parent() {
        return $this->belongsTo('App\Rubric', 'parent_id');
    }

    public function childs() {
        return $this
            ->hasMany('App\Rubric', 'parent_id')
            ->orderByRaw('rank ASC');
    }

    public function hasChilds() {
        return $this->childs->count() > 0;
    }

    public function posts()
    {
        return $this
            ->hasMany('App\Post')
            ->orderByRaw('updated_at DESC, title ASC');
    }

    public function havePosts() {
        return $this->posts->count() > 0;
    }

    public function groups(array $types = NULL) {
        return $this
            ->belongsToMany('App\Group')
            ->when($types, function ($groups) use ($types) {
                $groups->whereIn('type', $types);
            })
            ->orderByRaw('name ASC');
    }

    public function users()
    {
        return $this
            ->belongsToMany('App\User')
            ->orderByRaw('name ASC, first_name ASC');
    }
    public function notifications()
    {
        return $this
            ->hasManyThrough('App\Notification', 'App\Post')
            ->orderBy('created_at', 'DESC');
    }

    public function isFavorite() {
        return $this->users->find(auth()->user()->id) ? TRUE : FALSE;
    }

    public function route() {
        return $this->is_parent ?
            '' :
            '/'.(
                $this->parent_id ?
                    $this->parent->segment.AP::RUBRIC_SEPARATOR :
                    ''
            ).$this->segment;
    }

    public function identity() {
        return $this->name.($this->parent_id ? " ({$this->parent->name})" : '');
    }

    public static function sort() {
        return self::query()
            ->orderByRaw('position ASC, rank ASC')
            ->get();
    }

    public static function getRubrics(string $position) {
        return self::query()
            ->where('position', $position)
            ->where('rank', 'NOT LIKE', '%-%')
            ->orderBy('rank')
            ->get();
    }

    public static function filter(array $filter) {
        extract($filter);

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            /*->when($is_parent, function ($query) {
                $query->where('is_parent', TRUE);
            })*/;
    }
}
