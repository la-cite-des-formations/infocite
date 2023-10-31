<?php

namespace App;

use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'first_name', 'email', 'password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['remember_token',];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'account_expires_on' => 'date:Y-m-d',
        'birthday' => 'date:Y-m-d',
        'email_verified_at' => 'datetime',
    ];

    public function actor() {
        return $this
            ->belongsTo('App\Actor', 'id');
    }

    public function myPosts() {
        return $this
            ->hasMany('App\Post', 'author_id')
            ->orderByRaw('updated_at DESC');
    }

    public function updatedPosts() {
        return $this
            ->hasMany('App\Post', 'updated_by')
            ->orderByRaw('updated_at DESC');
    }

    public function commentedPosts() {
        return Post::query()
            ->whereIn('id', $this
                ->myComments()
                ->groupBy('post_id')
                ->pluck('post_id')
            )
            ->orderBy('created_at', 'DESC');
    }

    public function myFavoritesPosts() {
        return $this
            ->belongsToMany('App\Post', 'post_user')
            ->orderBy('created_at', 'DESC')
            ->withPivot(['is_favorite'])
            ->where('is_favorite', TRUE);
    }

    public function myNotifications() {
        $myNotifications = new Collection();

        $this->rubrics->each(function ($rubric) use (&$myNotifications) {
            $myNotifications = $myNotifications->merge($rubric->notifications);
        });

        $this->myFavoritesPosts->each(function ($post) use (&$myNotifications) {
            $myNotifications = $myNotifications->merge($post->notifications);
        });

        return $myNotifications;
    }

    public function oldNotifications() {
        return $this->myNotifications()->reject(function ($notification) {
            return $this->newNotifications->contains('id', $notification->id);
        });
    }

    public function newNotifications() {
        return $this
            ->belongsToMany('App\Notification');
    }

    public function myComments() {
        return $this
            ->hasMany('App\Comment')
            ->orderBy('created_at', 'DESC');
    }

    public function rubrics() {
        return $this
            ->belongsToMany('App\Rubric')
            ->orderByRaw('position, segment, rank');
    }

    public function myRubrics() {
        $myRubrics = $this->rubrics;

        $this->groups->each(function ($group) use (&$myRubrics) {
            $myRubrics = $myRubrics->merge($group->rubrics);
        });

        $this->profiles->each(function ($profile) use (&$myRubrics) {
            $myRubrics = $myRubrics->merge($profile->myRubrics());
        });

        return $myRubrics;
    }

    public function profiles() {
        return $this
            ->belongsToMany('App\User', 'profile_user', 'user_id', 'profile_id')
            ->orderByRaw('name ASC, first_name ASC')
            ->withTimestamps();
    }

    public function users() {
        return $this
            ->belongsToMany('App\User', 'profile_user', 'profile_id', 'user_id')
            ->orderByRaw('name ASC, first_name ASC')
            ->withTimestamps();
    }

    public function apps() {
        return $this
            ->belongsToMany('App\App')
            ->orderByRaw('name ASC')
            ->withPivot(['login', 'password']);
    }

    public function myApps() {
        $myApps = $this->apps;

        $this->groups->each(function ($group) use (&$myApps) {
            $myApps = $myApps->merge($group->apps);
        });

        $this->profiles->each(function ($profile) use (&$myApps) {
            $myApps = $myApps->merge($profile->myApps());
        });

        return $myApps->isEmpty() ?
            $myApps :
            $myApps
                ->toQuery()
                ->orderBy('name', 'ASC')
                ->get();
    }

    public function processes() {
        return $this
            ->groups(['P'])->get()
            ->filter(function ($group) {
                return is_object($group->process);
            })
            ->pluck('process');
    }

    public function subordinates() {
        return $this
            ->hasManyThrough('App\User', 'App\Actor', 'manager_id', 'id', 'id', 'id')
            ->orderByRaw('name ASC, first_name ASC');
    }

    public function manager() {
        return $this
            ->hasOneThrough('App\User', 'App\Actor', 'id', 'id', 'id', 'manager_id');
    }

    public function groups(array $types = NULL)
    {
        return $this
            ->belongsToMany('App\Group')
            ->when($types, function ($groups) use ($types) {
                $groups->whereIn('type', $types);
            })
            ->orderByRaw('name ASC')
            ->withPivot('function');
    }

    public function myGroups(array $types = NULL)
    {
        $myGroups = $this->groups($types)->get();

        $this->profiles->each(function ($profile) use (&$myGroups, $types) {
            $myGroups = $myGroups->merge($profile->myGroups($types));
        });

        return $myGroups;
    }

    public function groupsList(array $types = NULL, string $format = "%%", string $noResult = '')
    {
        $result = $this
            ->myGroups($types)
            ->pluck('name')
            ->implode(', ');

        return $result ? str_replace("%%", $result, $format) : $noResult;
    }

    public function processesList(string $format = "%%", string $noResult = '')
    {
        $result = $this
            ->processes()
            ->pluck('name')
            ->implode(', ');

        return $result ? str_replace("%%", $result, $format) : $noResult;
    }

    public function personalRights() {
        return $this
            ->morphToMany('App\Right', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC');
    }

    public function profilesRights() {
        $profilesRights = new Collection();

        $this->profiles->each(function ($profile) use ($profilesRights) {
            if ($profile->allRights()->isNotEmpty()) {
                $profilesRights->push((object) ['profile' => $profile->first_name, 'rights' => $profile->allRights()]);
            }
        });

        return $profilesRights;
    }

    public function groupsRights() {
        $groupsRights = new Collection();

        $this->groups->each(function ($group) use (&$groupsRights) {
            $groupsRights = $groupsRights->concat($group->rights);
        });

        return $groupsRights;
    }

    public function allRights() {
        $allRights = $this->personalRights->concat($this->groupsRights());

        $this->profiles->each(function ($profile) use (&$allRights) {
            $allRights = $allRights->concat($profile->allRights());
        });

        return $allRights;
    }

    public function getRightableRoles() {
        if (isset($this->pivot)) {
            $roles = NULL;
            foreach(Roles::all()->collection as $role) {
                if ($this->pivot->roles & $role->flag) $roles[] = $role->name;
            }
            return implode(', ', $roles ?? ['aucun']);
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

    public function rightsResourceable() {
        $rightsResourceable[] = '';
        if (!empty($this->pivot->resource_type)) {
            $rightsResourceable[] = $this->pivot->resource_type;
            $rightsResourceable[] = $this->pivot->resource_id;
        }
        return implode('|', $rightsResourceable);
    }

    public function function(int $groupId, string $format = "%%", string $noResult = '') {
        $result = $this
            ->groups
            ->find($groupId)
            ->pivot->function;

        return isset($result) ? str_replace("%%", $result, $format) : $noResult;
    }

    public function functionsList(array $types, string $format = "%%", string $noResult = '') {
        $result = $this
            ->groups($types)
            ->whereNotNull('function')
            ->pluck('function')
            ->implode(', ');

        return $result ? str_replace("%%", $result, $format) : $noResult;
    }

    public function hasRole(string $right, int $role, string $resource_type = NULL, int $resource_id = NULL, bool $extended = TRUE) {
        $rightable = $this
            ->allRights()
            ->where('name', $right)
            ->pluck('pivot')
            ->where('resource_type', $resource_type)
            ->where('resource_id', $resource_id)
            ->sortByDesc('priority')
            ->first();

        if (is_null($rightable)) {
            if (isset($resource_type) && isset($resource_id) && $extended) {
                return $this->hasRole($right, $role);
            }
            return NULL;
        }
        else {
            return $rightable->roles & $role;
        }
    }

    public function isManager() {
        $isManager = FALSE;

        Process::all()->each(function ($process) use(&$isManager) {
            if ($this->id == $process->manager_id) {
                return $isManager = TRUE;
            }
        });

        return $isManager;
    }

    public function isProfile() {
        return $this->name == AP::PROFILE;
    }

    public function identity() {
        return $this->isProfile() ?
            "{$this->first_name} (profil)" :
            "{$this->first_name} {$this->name}";
    }

    public function getIdentityAttribute() {
        return $this->isProfile() ?
            "{$this->first_name} (profil)" :
            "{$this->first_name} {$this->name}";
    }

    public function getInfo($userInfo) {
        extract($userInfo);

        switch (TRUE) {
            case $header === 'Statut' && $this->is_staff :
                return "Personnel{$this->groupsList(['P'], ' (%%)')}";

            case $header === 'Statut' && !$this->is_staff :
                return "Apprenant{$this->groupsList(['C'], ' (%%)')}";

            case $header === 'Classe' :
            case $header === 'Service' :
            case $header === 'Groupes' :
                return $this->groupsList([$groupType], '%%', '-');

            case $header === 'Processus' :
                return $this->processesList('%%', '-');

            case $header === 'Fonction' && !$groupId :
                return $this->functionsList(['P']) ?: $this->groupsList(['P'], '(%%)');

            case $header === 'Fonction' && $groupId && $groupType !== 'E':
                return $this->function($groupId, '%%', '-');

            case $header === 'Fonction' && $groupId && $groupType === 'E':
                $groupFunction = $this->function($groupId);
                return (new Collection([$this->functionsList(['P']) ?: $this->groupsList(['P'], '(%%)')]))
                    ->when($groupFunction, function ($collection) use ($groupFunction) {
                        return $collection->push($groupFunction);
                    })
                    ->implode(', ');
        }
    }

    public static function sort() {
        return self::query()
            ->orderByRaw('name ASC, first_name ASC')
            ->get();
    }

    public static function filter(array $filter) {
        extract($filter);

        $isFrozen = isset($isFrozen) ? ($isFrozen !=='' ? $isFrozen : NULL) : NULL;
        $showUndefinedLinks = isset($showUndefinedLinks) ? $showUndefinedLinks : NULL;

        switch (TRUE) {

            case $groupType && !$groupId :
                $users = new Collection();
                foreach(Group::where('type', $groupType[0])->get() as $group) {
                    $users = $users->merge($group->users);
                }
                $users = $users->toQuery();
                break;

            case $groupType && $groupId :
                $users = Group::find($groupId)->users();
                break;

            default : $users = self::query();
        }

        return $users
            ->when(!$profiles, function ($query) {
                $query->where('name', '<>', AP::PROFILE);
            })
            ->when($profiles, function ($query) {
                $query->where('name', AP::PROFILE);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("CONCAT(name, ' ', first_name) LIKE ?", "%{$search}%");
            })
            ->when(isset($isFrozen), function ($query) use ($isFrozen) {
                $query->where('is_frozen', $isFrozen);
            })
            ->when(isset($showUndefinedLinks), function ($query) use ($showUndefinedLinks) {
                $actors = $query
                    ->get()
                    ->filter(function ($actor) use ($showUndefinedLinks) {
                        return (is_null($actor->manager) && $showUndefinedLinks) || !$showUndefinedLinks;
                    });

                if ($actors->isEmpty()) {
                    $query->where('id', NULL);
                }
                else {
                    return $actors->toQuery();
                }
            });
    }
}
