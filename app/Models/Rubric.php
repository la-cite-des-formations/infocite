<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente une rubrique (catégorie) de messages dans le portail.
 * Les rubriques peuvent être imbriquées (parent/enfant) et contenir des messages (Posts).
 */
class Rubric extends Model
{
    use WithSearching;
    use \App\Models\Traits\HasFavorites;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'description', 'icon', 'is_parent', 'parent_id', 'position', 'rank', 'contains_posts', 'segment'];

    /**
     * Relation vers la rubrique parente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        return $this->belongsTo('App\Models\Rubric', 'parent_id');
    }

    /**
     * Relation vers les rubriques enfants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs() {
        return $this
            ->hasMany('App\Models\Rubric', 'parent_id')
            ->orderByRaw('rank ASC');
    }

    /**
     * Vérifie si la rubrique possède des enfants.
     *
     * @return bool
     */
    public function hasChilds() {
        return $this->childs->count() > 0;
    }

    /**
     * Relation vers les messages (Posts) contenus dans cette rubrique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this
            ->hasMany('App\Models\Post')
            ->orderByRaw('updated_at DESC, title ASC');
    }

    /**
     * Vérifie si la rubrique contient des messages.
     *
     * @return bool
     */
    public function havePosts() {
        return $this->posts->count() > 0;
    }

    /**
     * Relation vers les groupes ayant accès à cette rubrique.
     *
     * @param array|null $types Filtrer par types de groupe.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(? array $types = NULL) {
        return $this
            ->belongsToMany('App\Models\Group')
            ->when($types, function ($groups) use ($types) {
                $groups->whereIn('type', $types);
            })
            ->orderByRaw('name ASC');
    }

    /**
     * Relation vers les rubriques parents (navigation).
     */

    /**
     * Récupère les groupes ayant des droits spécifiques sur les messages de cette rubrique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groupsWithRubricPostsRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->groups()
            ->where('resource_type', 'Rubric')
            ->where('resource_id', $this->id);
    }

    /**
     * Récupère les utilisateurs réels ayant des droits spécifiques sur les messages de cette rubrique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usersWithRubricPostsRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->realUsers()
            ->where('resource_type', 'Rubric')
            ->where('resource_id', $this->id);
    }

    /**
     * Récupère les profils ayant des droits spécifiques sur les messages de cette rubrique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profilesWithRubricPostsRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->profiles()
            ->where('resource_type', 'Rubric')
            ->where('resource_id', $this->id);
    }


    /**
     * Retourne le chemin relatif (URL) vers la rubrique.
     * Gère la concaténation des segments parent/enfant.
     *
     * @return string
     */
    public function route() {
        return $this->is_parent ?
            '' :
            '/'.(
                $this->parent_id ?
                    $this->parent->segment.AP::RUBRIC_SEPARATOR :
                    ''
            ).$this->segment;
    }

    /**
     * Retourne l'identité complète de la rubrique (Nom + Parent éventuel).
     *
     * @return string
     */
    public function identity() {
        return $this->name.($this->parent_id ? " ({$this->parent->name})" : '');
    }

    /**
     * Accesseur retournant la position globale lisible (Libellé position + Rang).
     *
     * @return string
     */
    public function getGlobalPositionAttribute() {
        return AP::getRubricPosition($this->position).AP::betweenBrackets($this->position.$this->rank);
    }

    /**
     * Retourne toutes les rubriques triées par position puis rang.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function sort() {
        return self::query()
            ->orderByRaw('position ASC, rank ASC')
            ->get();
    }

    /**
     * Récupère les rubriques pour une position donnée (ex: 'Top', 'Side').
     * Exclut les rubriques avec un rang contenant un tiret (sous-rubriques spécifiques).
     *
     * @param string $position Nom de la position.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRubrics(string $position) {
        return self::query()
            ->where('position', $position)
            ->where('rank', 'NOT LIKE', '%-%')
            ->orderBy('rank')
            ->get();
    }

    /**
     * Filtre les rubriques selon un critère de recherche.
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $rubrics = static::query()
            ->with('parent')
            ->get()
            ->when($search, function ($rubrics) use ($search) {
                return $rubrics->filter(function ($rubric) use ($search) {
                    $columns = [$rubric->name, $rubric->global_position];
                    if (is_object($rubric->parent)) $columns[] = $rubric->parent->name;

                    return static::tableContains($columns, $search);
                });
            });

        return $rubrics->isEmpty() ? static::whereNull('id') : $rubrics->toQuery();
    }

    /**
     * Récupère toutes les rubriques configurées pour contenir des messages directes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function allWithPosts() {
        return static::query()
            ->whereRaw('contains_posts')
            ->orderByRaw('position, rank')
            ->get();
    }
}
