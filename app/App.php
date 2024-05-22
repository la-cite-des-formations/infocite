<?php

namespace App;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use WithSearching;
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

    public function isInstitutional() {
        return !$this->owner_id;
    }

    public function isPersonal() {
        return (boolean) $this->owner_id;
    }

    public function owner() {
        return User::find($this->owner_id);
    }

    public function identity($userId = NULL) {
        return isset($userId) && $userId == $this->owner_id ? "{$this->name} (appli personnelle)" : $this->name;
    }

    public static function sort() {
        return self::query()
            ->orderByRaw('name ASC')
            ->get();
    }

    public static function filter(array $filter) {
        extract($filter);

        $apps = static::query()
            ->when($authType, function ($query) use ($authType) {
                $query->where('auth_type', $authType);
            })
            ->when($type == 'P', function ($query) {
                $query->whereNotNull('owner_id');
            })
            ->when($type == 'I', function ($query) {
                $query->whereNull('owner_id');
            })
            ->orderBy('name')
            ->get()
            ->when($search, function ($apps) use ($search) {
                return $apps->filter(function ($app) use ($search) {
                    return static::tableContains([
                        $app->name,
                        $app->url,
                    ], $search);
                });
            });

        return $apps->isEmpty() ? static::whereNull('id') : $apps->toQuery();
    }
}
