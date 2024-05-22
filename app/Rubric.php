<?php

namespace App;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    use WithSearching;

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

    public function realUsers()
    {
        return $this
            ->belongsToMany('App\User')
            ->where('name', '<>', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC');
    }

    public function profiles()
    {
        return $this
            ->belongsToMany('App\User')
            ->where('name', AP::PROFILE)
            ->orderByRaw('first_name ASC');
    }

    public function groupsWithRubricPostsRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->groups()
            ->where('resource_type', 'Rubric')
            ->where('resource_id', $this->id);
    }

    public function usersWithRubricPostsRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->realUsers()
            ->where('resource_type', 'Rubric')
            ->where('resource_id', $this->id);
    }

    public function profilesWithRubricPostsRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->profiles()
            ->where('resource_type', 'Rubric')
            ->where('resource_id', $this->id);
    }

    public function notifications()
    {
        return $this
            ->hasManyThrough('App\Notification', 'App\Post')
            ->orderByRaw('release_at DESC, created_at DESC');
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

    public function getGlobalPositionAttribute() {
        return AP::getRubricPosition($this->position).AP::betweenBrackets($this->position.$this->rank);
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

        $rubrics = static::query()
            ->get()
            ->when($search, function ($rubrics) use ($search) {
                return $rubrics->filter(function ($rubric) use ($search) {
                    $columns = [$rubric->name, $rubric->global_position];
                    if (is_object($rubric->parent)) $columns[] = $rubric->parent->name;

                    return static::tableContains($columns, $search);
                });
            });

        return $rubrics->isEmpty() ? static::whereNull('id') : $rubrics->toQuery();
    }

    public static function allWithPosts() {
        return static::query()
            ->whereRaw('contains_posts')
            ->orderByRaw('position, rank')
            ->get();
    }
}
