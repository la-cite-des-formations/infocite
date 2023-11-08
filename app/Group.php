<?php

namespace App;

use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code_ypareo', 'type', 'name', ];

    public function apps() {
        return $this
            ->belongsToMany('App\App')
            ->orderByRaw('name ASC');
    }

    public function users()
    {
        return $this
            ->belongsToMany('App\User')
            ->where('name', '<>', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot('function');
    }

    public function profiles()
    {
        return $this
            ->belongsToMany('App\User')
            ->where('name', AP::PROFILE)
            ->orderBy('first_name')
            ->withPivot('function');
    }

    public function actors() {
        return $this
            ->belongsToMany('App\Actor', 'group_user', 'group_id', 'user_id');
    }

    public function rubrics() {
        return $this
            ->belongsToMany('App\Rubric')
            ->orderByRaw('position, segment, rank');
    }

    public function rights() {
        return $this
            ->morphToMany('App\Right', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC');
    }

    public function process() {
        return $this
            ->hasOne('App\Process');
    }

    public function getRightableRoles() {
        if (isset($this->pivot)) {
            $roles = NULL;
            foreach(Roles::all()->collection as $role) {
                if ($this->pivot->roles & $role->flag) $roles[] = $role->name;
            }
            return implode(', ', $roles ?? [Roles::NONE_STRING]);
        }
        return;
    }

    public function rightsResourceableString() {
        if (!empty($this->pivot->resource_type)) {
            $class = "\\App\\{$this->pivot->resource_type}";
            $entity = AP::getResourceable($this->pivot->resource_type);
            return " - {$entity} : {$class::find($this->pivot->resource_id)->identity()}";
        }
        return NULL;
    }

    public function rightsResourceable() {
        $rightsResourceable[] = '';
        if (!empty($this->pivot->resource_type)) {
            $rightsResourceable[] = $this->pivot->resource_type;
            $rightsResourceable[] = $this->pivot->resource_id;
        }
        return implode('|', $rightsResourceable);
    }

    public function identity() {
        $groupeType = AP::getGroupType($this->type);
        return "{$this->name} ({$groupeType})";
    }

    public static function sort() {
        return self::query()
            ->orderByRaw('type ASC, name ASC')
            ->get();
    }

    public static function filter(array $filter) {
        extract($filter);

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            });
    }
}
