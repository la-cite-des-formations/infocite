<?php

namespace App\Models\Traits;

use App\CustomFacades\AP;
use App\Models\Right;
use App\Models\Roles;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Gère l'ensemble des relations et méthodes liées aux permissions et droits
 * héritées par un utilisateur (droits personnels, liés à un profil ou un groupe).
 */
trait HasPermissions
{
    /**
     * Relation polymorphique vers les droits personnels de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function personalRights() {
        return $this
            ->morphToMany(Right::class, 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC');
    }

    /**
     * Récupère les droits hérités de ses profils.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function profilesRights() {
        $profilesRights = new Collection();

        $this->profiles->each(function ($profile) use ($profilesRights) {
            if ($profile->allRights()->isNotEmpty()) {
                $profilesRights->push((object) ['profile' => $profile->first_name, 'rights' => $profile->allRights()]);
            }
        });

        return $profilesRights;
    }

    /**
     * Récupère les droits hérités de ses groupes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function groupsRights() {
        $groupsRights = new Collection();

        $this->groups->each(function ($group) use (&$groupsRights) {
            $groupsRights = $groupsRights->concat($group->rights);
        });

        return $groupsRights;
    }

    /**
     * Récupère l'intégralité des droits de l'utilisateur (personnels, groupes, profils).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected $allRightsCache = null;

    public function allRights() {
        if ($this->allRightsCache !== null) {
            return $this->allRightsCache;
        }

        $allRights = $this->personalRights->concat($this->groupsRights());

        $this->profiles->each(function ($profile) use (&$allRights) {
            $allRights = $allRights->concat($profile->allRights());
        });

        return $this->allRightsCache = $allRights;
    }

    /**
     * Retourne les noms des rôles associés au droit courant (via pivot).
     *
     * @return string|null
     */
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

    /**
     * Retourne une description textuelle de la ressource associée au droit.
     *
     * @return string
     */
    public function rightsResourceableString() {
        if (!empty($this->pivot->resource_type)) {
            $class = "\\App\\Models\\{$this->pivot->resource_type}";
            $entity = AP::getResourceable($this->pivot->resource_type);
            return " - {$entity} : {$class::find($this->pivot->resource_id)->identity()}";
        }
        return "";
    }

    /**
     * Retourne une chaîne formatée Type|ID de la ressource.
     *
     * @return string
     */
    public function rightsResourceable() {
        $rightsResourceable[] = '';
        if (!empty($this->pivot->resource_type)) {
            $rightsResourceable[] = $this->pivot->resource_type;
            $rightsResourceable[] = $this->pivot->resource_id;
        }
        return implode('|', $rightsResourceable);
    }

    /**
     * Récupère l'objet pivot de droit correspondant à un droit et une ressource.
     *
     * @param string $right Nom du droit.
     * @param string|null $resource_type Type de ressource.
     * @param int|null $resource_id ID de ressource.
     * @return mixed
     */
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

    /**
     * Vérifie si l'utilisateur possède EXACTEMENT un rôle pour un droit donné.
     *
     * @param string $right Nom du droit.
     * @param int $role Valeur binaire du rôle.
     * @param string|null $resource_type Type de ressource.
     * @param int|null $resource_id ID de ressource.
     * @param bool $extended Si vrai, vérifie aussi au niveau global si non trouvé sur la ressource.
     * @return bool|null
     */
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

    /**
     * Vérifie si l'utilisateur possède un rôle (bitmask) pour un droit donné.
     *
     * @param string $right Nom du droit.
     * @param int $role Valeur binaire du rôle.
     * @param string|null $resource_type Type de ressource.
     * @param int|null $resource_id ID de ressource.
     * @param bool $extended Si vrai, vérifie aussi au niveau global.
     * @return int|null
     */
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

    /**
     * Récupère tous les utilisateurs réels (hors profils) possédant un certain rôle sur un droit donné.
     *
     * @param string $rightName Nom du droit.
     * @param int $roles Comparaison de bitmask de rôles.
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Sélectionne un ensemble d'utilisateurs selon des actions prédéfinies.
     *
     * @param string $action Nom de l'action prédéfinie.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function allWho(string $action) {
        switch ($action) {
            case 'can-comment-posts' :
                return static::haveOn('comments', Roles::IS_EDITR);

            case 'can-edit-posts' :
                return static::haveOn('posts', Roles::IS_EDITR);

            case 'have-edited-posts' :
                return static::query()
                    ->whereIn('id', \App\Models\Post::all()->pluck('author_id')->unique())
                    ->orderByRaw('name, first_name')
                    ->get();

            case 'have-commented-posts' :
                return static::query()
                    ->whereIn('id', \App\Models\Comment::all()->pluck('user_id')->unique())
                    ->orderByRaw('name, first_name')
                    ->get();

            case 'have-label' :
                return static::query()
                    ->whereIn('id', DB::table('referents')->pluck('id'));

            default :
                return static::all();
        }
    }
}
