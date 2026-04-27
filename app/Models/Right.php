<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente un droit (permission) au sein du système.
 * Les droits sont associés à des utilisateurs, groupes ou profils via la table polymorphique 'rightables'.
 */
class Right extends Model
{
    use WithSearching;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'description',
        'rd_role', 'rd_description',
        'ed_role', 'ed_description',
        'md_role', 'md_description',
        'ad_role', 'ad_description',
        'default_roles', 'dashboard_roles'
    ];

    /**
     * Relation vers les groupes possédant ce droit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function groups() {
        return $this
            ->morphedByMany('App\Models\Group', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, resource_type ASC, resource_id ASC');
    }

    /**
     * Récupère les groupes possédant ce droit filtrés par type.
     *
     * @param string $type Type de groupe.
     * @return \Illuminate\Support\Collection
     */
    public function groupsByType($type) {
        return $this
            ->groups()
            ->where('type', $type)
            ->get();
    }

    /**
     * Relation vers tous les utilisateurs possédant ce droit (incluant les profils).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users() {
        return $this
            ->morphedByMany('App\Models\User', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, first_name ASC, resource_type ASC, resource_id ASC');
    }

    /**
     * Relation vers les utilisateurs réels (hors profils) possédant ce droit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function realUsers() {
        return $this
            ->morphedByMany('App\Models\User', 'rightable')
            ->where('name', '<>', AP::PROFILE)
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC, first_name ASC, resource_type ASC, resource_id ASC');
    }

    /**
     * Relation vers les profils (modèles de droits) possédant ce droit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function profiles() {
        return $this
            ->morphedByMany('App\Models\User', 'rightable')
            ->where('name', AP::PROFILE)
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('first_name ASC, resource_type ASC, resource_id ASC');
    }

    /**
     * Retourne une chaîne descriptive des rôles associés au droit dans le contexte du pivot.
     * Décode le masque binaire des rôles.
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
     * Retourne une chaîne descriptive de la ressource associée au droit.
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
     * Retourne une chaîne descriptive des rôles définis dans le tableau de bord pour ce droit.
     *
     * @return string
     */
    public function rolesFromDashboard() {
        $dashboardRoles = NULL;
        foreach(Roles::all()->collection as $role) {
            if ($this->dashboard_roles & $role->flag) $dashboardRoles[] = $role->name;
        }
        return implode(', ', $dashboardRoles ?? [Roles::NONE_STRING]);
    }

    /**
     * Retourne une chaîne descriptive des rôles par défaut associés à ce droit.
     *
     * @return string
     */
    public function defaultRoles() {
        $defaultRoles = NULL;
        foreach(Roles::all()->collection as $role) {
            if ($this->default_roles & $role->flag) $defaultRoles[] = $role->name;
        }
        return implode(', ', $defaultRoles ?? [Roles::NONE_STRING]);
    }

    /**
     * Vérifie si un rôle spécifique est exercé depuis le tableau de bord.
     *
     * @param int $roleFlag Flag du rôle à tester.
     * @return int
     */
    public function exercisedFromDashboard($roleFlag) {
        return $this->dashboard_roles & $roleFlag;
    }

    /**
     * Vérifie si un rôle spécifique est activé par défaut.
     *
     * @param int $roleFlag Flag du rôle à tester.
     * @return int
     */
    public function byDefault($roleFlag) {
        return $this->default_roles & $roleFlag;
    }

    /**
     * Filtre les droits selon des critères de recherche.
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
