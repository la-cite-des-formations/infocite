<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use WithSearching;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'first_name', 'email', 'password', ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['remember_token', ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'account_expires_on' => 'date:Y-m-d',
        'email_verified_at' => 'datetime',
    ];

    public function learner()
    {
        return $this->hasOne(Learner::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function actor() {
        return $this
            ->belongsTo(Actor::class, 'id');
    }

    public function myPosts() {
        return $this
            ->hasMany(Post::class, 'author_id')
            ->orderByRaw('updated_at DESC');
    }

    public function postsRead() {
        return $this
            ->belongsToMany(Post::class)
            ->withPivot(['is_read'])
            ->where('is_read', TRUE)
            ->orderBy('created_at', 'DESC');
    }

    public function updatedPosts() {
        return $this
            ->hasMany(Post::class, 'corrector_id')
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
            ->belongsToMany(Post::class)
            ->orderBy('created_at', 'DESC')
            ->withPivot(['is_favorite'])
            ->where('is_favorite', TRUE)
            ->where('published', TRUE)
            ->where(function ($query) {
                $query
                    ->where('expired_at', '>', today()->format('Y-m-d'))
                    ->orWhere('expired_at', NULL);
            });
    }

    public function myFavoritesRubrics() {
        return $this
            ->belongsToMany(Rubric::class)
            ->orderBy('created_at', 'DESC')
            ->withPivot(['rubric_id']);

    }

    public function myNotifications() {
        $myNotifications = new Collection();

        $this->rubrics->each(function ($rubric) use (&$myNotifications) {
            $rubric->posts->each(function ($post) use (&$myNotifications) {
                $myNotifications = $myNotifications->merge($post->notifications);
            });
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
            ->belongsToMany(Notification::class);
    }

    public function myComments() {
        return $this
            ->hasMany(Comment::class)
            ->orderBy('created_at', 'DESC');
    }

    public function rubrics() {
        return $this
            ->belongsToMany(Rubric::class)
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
            ->belongsToMany(self::class, 'profile_user', 'user_id', 'profile_id')
            ->orderByRaw('name ASC, first_name ASC')
            ->withTimestamps();
    }

    public function users() {
        return $this
            ->belongsToMany(self::class, 'profile_user', 'profile_id', 'user_id')
            ->orderByRaw('name ASC, first_name ASC')
            ->withTimestamps();
    }

    public function apps() {
        return $this
            ->belongsToMany(App::class)
            ->orderByRaw('name ASC')
            ->withPivot(['login', 'password']);
    }

    public function personnalApps() {
        return $this
            ->hasMany(App::class, 'owner_id')
            ->orderByRaw('name ASC');
    }

    public function myApps() {
        $myApps = $this->apps;

        $this->groups->each(function ($group) use (&$myApps) {
            $myApps = $myApps->merge($group->apps);
        });

        $this->profiles->each(function ($profile) use (&$myApps) {
            $myApps = $myApps->merge($profile->myApps());
        });

        return $this->myFavoritesApps->merge($myApps->isEmpty() ?
            $myApps :
            $myApps
                ->sortBy('name')
        );
    }

    public function myFavoritesApps() {
        return $this
            ->belongsToMany(App::class, 'favorites_apps')
            ->withPivot(['rank'])
            ->orderBy('rank');
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
            ->hasManyThrough(self::class, Actor::class, 'manager_id', 'id', 'id', 'id')
            ->orderByRaw('name ASC, first_name ASC');
    }

    public function manager() {
        return $this
            ->hasOneThrough(self::class, Actor::class, 'id', 'id', 'id', 'manager_id');
    }

    public function groups(? array $types = NULL)
    {
        return $this
            ->belongsToMany(Group::class)
            ->when($types, function ($groups) use ($types) {
                $groups->whereIn('type', $types);
            })
            ->orderByRaw('name ASC')
            ->withPivot('function');
    }

    public function myGroups(? array $types = NULL)
    {
        $myGroups = $this->groups($types)->get();

        $this->profiles->each(function ($profile) use (&$myGroups, $types) {
            $myGroups = $myGroups->merge($profile->myGroups($types));
        });

        return $myGroups;
    }

    /**
     * Tous les numéros de téléphone de l'utilisateur
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Numéro de téléphone de l'utilisateur d'un type en particulier
     */
    public function phone($type) {
        return $this->phones->firstWhere('type', $type);
    }

    public function groupsList(? array $types = NULL, string $format = "%%", string $noResult = '')
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
            ->morphToMany(Right::class, 'rightable')
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
            return implode(', ', $roles ?? [Roles::NONE_STRING]);
        }
        return;
    }

    public function rightsResourceableString() {
        if (!empty($this->pivot->resource_type)) {
            $class = "\\App\\Models\\{$this->pivot->resource_type}";
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

    public function getRightable(string $right, ? string $resource_type = NULL, ? int $resource_id = NULL) {
        return $this
            ->allRights()
            ->where('name', $right)
            ->pluck('pivot')
            ->where('resource_type', $resource_type)
            ->where('resource_id', $resource_id)
            ->sortByDesc('priority')
            ->first();
    }

    public function hasStrictRole(string $right, int $role, ? string $resource_type = NULL, ? int $resource_id = NULL, bool $extended = TRUE) {
        $rightable = $this->getRightable($right, $resource_type, $resource_id);

        if (is_null($rightable)) {
            if (isset($resource_type) && isset($resource_id) && $extended) {
                return $this->hasStrictRole($right, $role);
            }
            return NULL;
        }
        else {
            return $rightable->roles & $role == $role;
        }
    }

    public function hasRole(string $right, int $role, ? string $resource_type = NULL, ? int $resource_id = NULL, bool $extended = TRUE) {
        $rightable = $this->getRightable($right, $resource_type, $resource_id);

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

        Chartnode::all()->each(function ($chartnode) use(&$isManager) {
            if ($this->id == $chartnode->manager_id) {
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

    public function getLabelAttribute() {
        $referent = DB::table('referents')
            ->where('id', $this->id)
            ->first();

        return is_object($referent) ? $referent->label : '';
    }

    public function getChartnodeIdentityAttribute() {
        return $this->identity.AP::betweenBrackets($this->label ?: '');
    }

    public function getProcessAttribute() {
        $processUser = Chartnode::query()
            ->where('code_fonction', $this->groups(['P'])->first()->code_ypareo)
            ->first();

        return is_object($processUser) ? $processUser->name : '';
    }

    public function getTodayConnectionRecordedAttribute() {
        return Connection::fromToday()
            ->where('user_id', auth()->user()->id)
            ->get()
            ->isNotEmpty();
    }

    public function getEditedPostsNbAttribute() {
        return $this->myPosts
            ->merge($this->updatedPosts)
            ->count();
    }

    public function getCommentedPostsNbAttribute() {
        return $this->commentedPosts()
            ->get()
            ->count();
    }

    public function getPersonalAppsNbAttribute() {
        return $this->personnalApps
            ->count();
    }

    public function getNotificationStatusAttribute() {
        switch (TRUE) {
            case $this->employee && !$this->employee->desktop_notifications_granted:
                return "Aucune";

            case $this->employee && $this->employee->desktop_notifications_granted && !$this->employee->notify_only_favorites:
                return "Toutes";

            case $this->employee && $this->employee->desktop_notifications_granted && $this->employee->notify_only_favorites:
                return "Favoris";

            default:
                return "Non disponibles";
        }
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
                Group::where('type', $groupType)
                    ->get()
                    ->each(function ($group) use (&$users) {
                        $users = $users->merge($group->users);
                    });
                $query = static::whereIn('id', $users->pluck('id'));
                break;

            case $groupType && $groupId :
                $query = Group::find($groupId)->users();
                break;

            default : $query = static::query();
        }

        $users = $query
            ->when(!$profiles, function ($query) {
                $query->where('name', '<>', AP::PROFILE);
            })
            ->when($profiles, function ($query) {
                $query->where('name', AP::PROFILE);
            })
            ->when(isset($isFrozen), function ($query) use ($isFrozen) {
                $query->where('is_frozen', $isFrozen);
            })
            ->get()
            ->when($search, function ($users) use ($search, $filter) {
                return $users->filter(function ($user) use ($search, $filter) {
                    $columns[] = $user->identity;

                    if (!$filter['profiles']) {
                        $columns[] = $user->getInfo(AP::getUserInfoParams($filter));
                    }

                    return static::tableContains($columns, $search);
                });
            });

        return $users->isEmpty() ? static::whereNull('id') : $users->toQuery();
    }

    public static function haveOn(string $rightName, int $roles) {
        return static::query()
            ->join('rightables', 'rightables.rightable_id', '=', 'users.id')
            ->join('rights', 'rightables.right_id', '=', 'rights.id')
            ->where('rightables.rightable_type', 'User')
            ->where('rights.name', $rightName)
            ->whereRaw('rightables.roles & '.$roles)
            ->where('users.name', '<>', AP::PROFILE)
            ->select('users.*')
            ->union(
                static::query()
                    ->join('profile_user', 'profile_user.user_id', '=', 'users.id')
                    ->whereIn('profile_user.profile_id', function ($query) use($rightName, $roles) {
                        $query->select('users.id')
                            ->from('users')
                            ->join('rightables', 'rightables.rightable_id', '=', 'users.id')
                            ->join('rights', 'rightables.right_id', '=', 'rights.id')
                            ->where('rightables.rightable_type', 'User')
                            ->where('rights.name', $rightName)
                            ->whereRaw('rightables.roles & '.$roles)
                            ->where('users.name', AP::PROFILE);
                    })
                    ->where('users.name', '<>', AP::PROFILE)
                    ->select('users.*')
            )
            ->union(
                static::query()
                    ->join('group_user', 'group_user.user_id', '=', 'users.id')
                    ->whereIn('group_user.group_id', function ($query) use($rightName, $roles) {
                        $query->select('groups.id')
                            ->from('groups')
                            ->join('rightables', 'rightables.rightable_id', '=', 'groups.id')
                            ->join('rights', 'rightables.right_id', '=', 'rights.id')
                            ->where('rightables.rightable_type', 'Group')
                            ->where('rights.name', $rightName)
                            ->whereRaw('rightables.roles & '.$roles);
                    })
                    ->where('users.name', '<>', AP::PROFILE)
                    ->select('users.*')
            )
            ->orderByRaw('name, first_name')
            ->distinct()
            ->get();
    }

    public static function allWho(string $action) {
        switch ($action) {
            case 'can-comment-posts' :
                return static::haveOn('comments', Roles::IS_EDITR);

            case 'can-edit-posts' :
                return static::haveOn('posts', Roles::IS_EDITR);

            case 'have-edited-posts' :
                return static::query()
                    ->whereIn('id', Post::all()->pluck('author_id')->unique())
                    ->orderByRaw('name, first_name')
                    ->get();

            case 'have-commented-posts' :
                return static::query()
                    ->whereIn('id', Comment::all()->pluck('user_id')->unique())
                    ->orderByRaw('name, first_name')
                    ->get();

            case 'have-label' :
                return static::query()
                    ->whereIn('id', DB::table('referents')->pluck('id'));

            default :
                return static::all();
        }
    }

    public static function activeEditors($filter = []) {
        extract($filter);
        $editorType = isset($editorType) ? $editorType : 'all';
        $rubric_id = isset($rubric_id) ? $rubric_id : NULL;

        return static::query()
            ->join('posts', function ($query) use ($editorType) {
                $query->when($editorType == 'all' || $editorType == 'authors', function ($join) {
                    $join->on('posts.author_id', '=', 'users.id');
                })->when($editorType == 'all' || $editorType == 'correctors', function ($join) {
                    $join->orOn('posts.corrector_id', '=', 'users.id');
                });
            })
            ->selectRaw('users.name, users.first_name, COUNT(*) AS posts_nb')
            ->when($rubric_id !== NULL, function ($query) use ($rubric_id) {
                $query->where('posts.rubric_id', $rubric_id);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('posts_nb DESC, users.name, users.first_name');
    }

    public static function activeCommentators($filter = []) {
        extract($filter);
        $byStaff = !isset($commentatorType) || ($commentatorType == 'all') ? NULL : $commentatorType == 'staff';

        return static::query()
            ->join('comments', 'comments.user_id', '=', 'users.id')
            ->selectRaw('users.name, users.first_name, COUNT(*) AS comments_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query->where('users.is_staff', $byStaff);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('comments_nb DESC, users.name, users.first_name');
    }

    public static function personalAppsUsers($filter = []) {
        extract($filter);
        $byStaff = !isset($userType) || ($userType == 'all') ? NULL : $userType == 'staff';

        return static::query()
            ->join('apps', 'apps.owner_id', '=', 'users.id')
            ->selectRaw('users.name, users.first_name, COUNT(*) AS apps_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query->where('users.is_staff', $byStaff);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('apps_nb DESC, users.name, users.first_name');
    }
}
