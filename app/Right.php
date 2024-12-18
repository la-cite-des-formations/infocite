<?php

namespace App;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
    use WithSearching;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'rd_role', 'rd_description',
        'ed_role', 'ed_description',
        'md_role', 'md_description',
        'ad_role', 'ad_description',
        'default_roles', 'dashboard_roles'
    ];

    public function groups() {
        return $this
            ->morphedByMany('App\Group', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, resource_type ASC, resource_id ASC');
    }

    public function groupsByType($type) {
        return $this
            ->groups()
            ->where('type', $type)
            ->get();
    }

    public function users() {
        return $this
            ->morphedByMany('App\User', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, first_name ASC, resource_type ASC, resource_id ASC');
    }

    public function realUsers() {
        return $this
            ->morphedByMany('App\User', 'rightable')
            ->where('name', '<>', AP::PROFILE)
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, first_name ASC, resource_type ASC, resource_id ASC');
    }

    public function profiles() {
        return $this
            ->morphedByMany('App\User', 'rightable')
            ->where('name', AP::PROFILE)
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('first_name ASC, resource_type ASC, resource_id ASC');
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
        return "";
    }

    public function rolesFromDashboard() {
        $dashboardRoles = NULL;
        foreach(Roles::all()->collection as $role) {
            if ($this->dashboard_roles & $role->flag) $dashboardRoles[] = $role->name;
        }
        return implode(', ', $dashboardRoles ?? [Roles::NONE_STRING]);
    }

    public function defaultRoles() {
        $defaultRoles = NULL;
        foreach(Roles::all()->collection as $role) {
            if ($this->default_roles & $role->flag) $defaultRoles[] = $role->name;
        }
        return implode(', ', $defaultRoles ?? [Roles::NONE_STRING]);
    }

    public function exercisedFromDashboard($roleFlag) {
        return $this->dashboard_roles & $roleFlag;
    }

    public function byDefault($roleFlag) {
        return $this->default_roles & $roleFlag;
    }

    public static function filter(array $filter) {
        extract($filter);

        $rights = static::query()
            ->get()
            ->when($search, function ($rights) use ($search) {
                return $rights->filter(function ($right) use ($search) {
                    return static::tableContains([
                        $right->name,
                        $right->rolesFromDashboard(),
                    ], $search);
                });
            });

        return $rights->isEmpty() ? static::whereNull('id') : $rights->toQuery();
    }
}
