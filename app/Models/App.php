<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente une application au sein du portail.
 *
 * Une application peut être :
 * - Institutionnelle (accessible à des groupes ou profils).
 * - Personnelle (créée par un utilisateur pour son propre usage).
 * - Mise en favoris par les utilisateurs.
 */
class App extends Model
{
    use WithSearching;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'url', 'icon', 'description', 'owner_id', 'auth_type'];

    /**
     * Relation vers les groupes ayant accès à cette application.
     *
     * @param array|null $types Filtrer par types de groupe.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(array $types = NULL) {
        return $this
            ->belongsToMany('App\Models\Group')
            ->when($types, function ($groups) use ($types) {
                $groups->whereIn('type', $types);
            })
            ->orderByRaw('name ASC');
    }

    /**
     * Relation vers les utilisateurs ayant accès à cette application (tous types confondus).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this
            ->belongsToMany('App\Models\User')
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['login', 'password']);
    }

    /**
     * Relation vers les utilisateurs réels (hors profils) ayant accès à l'application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function realUsers() {
        return $this
            ->belongsToMany('App\Models\User')
            ->where('name', '<>', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['login', 'password']);
    }

    /**
     * Relation vers les utilisateurs ayant mis cette application en favoris.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fanUsers() {
        return $this
            ->belongsToMany('App\Models\User', 'favorites_apps')
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['rank']);
    }

    /**
     * Relation vers les profils (modèles de droits) ayant accès à l'application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles() {
        return $this
            ->belongsToMany('App\Models\User')
            ->where('name', AP::PROFILE)
            ->orderByRaw('name ASC, first_name ASC')
            ->withPivot(['login', 'password']);
    }

    /**
     * Accesseur vérifiant si l'application est en favoris pour l'utilisateur authentifié.
     *
     * @return bool
     */
    public function getIsFavoriteAttribute() {
        return $this->fanUsers->contains('id', auth()->user()->id);
    }

    /**
     * Vérifie si l'application appartient à l'utilisateur authentifié.
     *
     * @return bool
     */
    public function isMine() {
        return $this->owner_id === auth()->user()->id;
    }

    /**
     * Vérifie si l'application est institutionnelle (pas de propriétaire).
     *
     * @return bool
     */
    public function isInstitutional() {
        return !$this->owner_id;
    }

    /**
     * Vérifie si l'application est personnelle.
     *
     * @return bool
     */
    public function isPersonal() {
        return (boolean) $this->owner_id;
    }

    /**
     * Récupère le propriétaire de l'application.
     *
     * @return User|null
     */
    public function owner() {
        return User::find($this->owner_id);
    }

    /**
     * Retourne l'identité de l'application (Nom + mention si personnelle).
     *
     * @return string
     */
    public function identity() {
        return $this->name.($this->owner_id ? ' (appli personnelle)' : '');
    }

    /**
     * Retourne toutes les applications triées par nom.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function sort() {
        return self::query()
            ->orderByRaw('name ASC')
            ->get();
    }

    /**
     * Filtre les applications selon des critères (recherche, type, mode d'authentification).
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
