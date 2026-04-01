<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente un groupe d'utilisateurs.
 * Les groupes sont utilisés pour la gestion des droits et l'accès aux applications/rubriques.
 */
class Group extends Model
{
    use WithSearching;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['code_ypareo', 'type', 'name', 'public'];

    /**
     * Relation vers les applications accessibles à ce groupe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps() {
        return $this
            ->belongsToMany('App\Models\App')
            ->orderByRaw('name ASC');
    }

    /**
     * Relation vers les utilisateurs réels membres du groupe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this
            ->belongsToMany('App\Models\User')
            ->where('name', '<>', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot('function');
    }

    /**
     * Relation vers les profils rattachés à ce groupe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles()
    {
        return $this
            ->belongsToMany('App\Models\User')
            ->where('name', AP::PROFILE)
            ->orderBy('first_name')
            ->withPivot('function');
    }

    /**
     * Relation vers les rubriques accessibles à ce groupe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rubrics() {
        return $this
            ->belongsToMany('App\Models\Rubric')
            ->orderByRaw('position, segment, rank');
    }

    /**
     * Relation polymorphique vers les droits accordés à ce groupe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function rights() {
        return $this
            ->morphToMany('App\Models\Right', 'rightable')
            ->withPivot(['resource_type', 'resource_id', 'priority', 'roles'])
            ->orderByRaw('name ASC');
    }

    /**
     * Retourne une chaîne descriptive des rôles associés au groupe (via pivot).
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
     * Retourne une chaîne descriptive de la ressource associée aux droits du groupe.
     *
     * @return string|null
     */
    public function rightsResourceableString() {
        if (!empty($this->pivot->resource_type)) {
            $class = "\\App\\Models\\{$this->pivot->resource_type}";
            $entity = AP::getResourceable($this->pivot->resource_type);
            return " - {$entity} : {$class::find($this->pivot->resource_id)->identity()}";
        }
        return NULL;
    }

    /**
     * Encode les informations de ressource des droits pour usage interne.
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
     * Retourne l'identité complète du groupe (Nom + Type).
     *
     * @return string
     */
    public function identity() {
        $groupeType = AP::getGroupType($this->type);
        return "{$this->name} ({$groupeType})";
    }

    /**
     * Retourne tous les groupes triés par type puis nom.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function sort() {
        return self::query()
            ->orderByRaw('type ASC, name ASC')
            ->get();
    }

    /**
     * Filtre les groupes selon des critères (type, recherche).
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $groups = static::query()
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->get()
            ->when($search, function ($groups) use ($search) {
                return $groups->filter(function ($group) use ($search) {
                    return static::tableContains([
                        $group->name,
                        AP::getGroupType($group->type),
                    ], $search);
                });
            });

        return $groups->isEmpty() ? static::whereNull('id') : $groups->toQuery();
    }
}
