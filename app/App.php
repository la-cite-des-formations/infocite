<?php

namespace App;

use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'url', 'icon', 'description', 'owner_id', 'auth_type'];

    public function groups(array $types = NULL) {
        return $this
            ->belongsToMany('App\Group')
            ->when($types, function ($groups) use ($types) {
                $groups->whereIn('type', $types);
            })
            ->orderByRaw('name ASC');
    }

    public function users() {
        return $this
            ->belongsToMany('App\User')
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['login', 'password']);
    }

    public function realUsers() {
        return $this
            ->belongsToMany('App\User')
            ->where('name', '<>', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['login', 'password']);
    }

    public function profiles() {
        return $this
            ->belongsToMany('App\User')
            ->where('name', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['login', 'password']);
    }

    public function isMine() {
        return $this->owner_id === auth()->user()->id;
    }

    public function owner() {
        return User::find($this->owner_id);
    }

    public function identity() {
        return $this->isMine() ? "{$this->name} (personnelle)" : $this->name;
    }

    public static function sort() {
        return self::query()
            ->orderByRaw('name ASC')
            ->get();
    }

    public static function filter(array $filter) {
        extract($filter);

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->when($authType, function ($query) use ($authType) {
                $query->where('auth_type', $authType);
            })
            ->when($type == 'P', function ($query) {
                $query->whereNotNull('owner_id');
            })
            ->when($type == 'I', function ($query) {
                $query->whereNull('owner_id');
            })
            ->orderBy('name');
    }
}
