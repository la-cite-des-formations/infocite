<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
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

    public function users() {
        return $this
            ->morphedByMany('App\User', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, first_name ASC, resource_type ASC, resource_id ASC');
    }

    public function rolesFromDashboard() {
        $dashboardRoles = NULL;
        foreach(Roles::all()->collection as $role) {
            if ($this->dashboard_roles & $role->flag) $dashboardRoles[] = $role->name;
        }
        return implode(', ', $dashboardRoles ?? ['aucun']);
    }

    public function defaultRoles() {
        $defaultRoles = NULL;
        foreach(Roles::all()->collection as $role) {
            if ($this->default_roles & $role->flag) $defaultRoles[] = $role->name;
        }
        return implode(', ', $defaultRoles ?? ['aucun']);
    }

    public function exercisedFromDashboard($roleFlag) {
        return $this->dashboard_roles & $roleFlag;
    }

    public function byDefault($roleFlag) {
        return $this->default_roles & $roleFlag;
    }

    public static function filter(array $filter) {
        extract($filter);

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('description', 'like', "%$search%");
            });
    }
}
