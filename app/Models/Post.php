<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use App\Models\Traits\HasInteractions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Représente un article (Post) publié dans une rubrique.
 * Gère le cycle de vie des contenus : rédaction, publication, expiration, archivage.
 */
class Post extends Model
{
    use WithSearching;
    use HasInteractions;
    use \App\Models\Traits\HasFavorites;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['title', 'content', 'icon', 'rubric_id', 'author_id', 'updated_by', 'published_at', 'expired_at', 'is_acknowledgment_required'];

    /** @var array<string, bool> Valeurs par défaut pour les attributs. */
    protected $attributes = ['published' => FALSE, 'auto_delete' => FALSE];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at'               => 'date:Y-m-d',
        'expired_at'                 => 'date:Y-m-d',
        'is_acknowledgment_required' => 'boolean',
    ];

    /**
     * Relation vers la rubrique parente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rubric()
    {
        return $this->belongsTo('App\Models\Rubric');
    }

    /**
     * Relation vers l'utilisateur ayant corrigé/mis à jour l'article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function corrector()
    {
        return $this->belongsTo('App\Models\User', 'corrector_id');
    }

    /**
     * Relation vers l'auteur original de l'article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\Models\User', 'author_id');
    }

    /**
     * Relation vers les commentaires associés à l'article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments() {
        return $this
            ->hasMany('App\Models\Comment')
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Relation vers les utilisateurs ayant lu ou mis en favori l'article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readers() {
        return $this
            ->belongsToMany('App\Models\User')
            ->withPivot(['is_read', 'tags']);
    }

    /**
     * Récupère la liste des utilisateurs à notifier pour cet article.
     * Combine les utilisateurs ayant mis la rubrique en favori et ceux ayant mis l'article lui-même en favori.
     *
     * @return \Illuminate\Support\Collection
     */
    public function notificableReaders() {
        $notificableUsers = collect();

        if ($this->rubric) {
            $notificableUsers = $notificableUsers->merge($this->rubric->favoritedBy);
        }

        return $notificableUsers->merge($this->favoritedBy)->unique('id');
    }

    /**
     * Relation vers les notifications générées par cet article.
     *
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this
            ->morphMany(Notification::class, 'object')
            ->orderByRaw('release_at DESC, created_at DESC');
    }

    /**
     * Récupère les groupes ayant des droits spécifiques sur cet article.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function groupsWithPostRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->groups()
            ->where('resource_type', 'Post')
            ->where('resource_id', $this->id);
    }

    /**
     * Récupère les utilisateurs réels ayant des droits spécifiques sur cet article.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function usersWithPostRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->realUsers()
            ->where('resource_type', 'Post')
            ->where('resource_id', $this->id);
    }

    /**
     * Récupère les profils ayant des droits spécifiques sur cet article.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function profilesWithPostRight() {
        return Right::query()
            ->where('name', 'posts')
            ->first()
            ->profiles()
            ->where('resource_type', 'Post')
            ->where('resource_id', $this->id);
    }

    /**
     * Accesseur pour savoir si l'utilisateur courant a vu le post aujourd'hui.
     *
     * @return bool
     */
    public function getViewTodayAttribute(): bool
    {
        return $this->hasInteraction('view');
    }

    /**
     * Accesseur pour savoir si l'utilisateur courant a créer/modifier le post aujourd'hui.
     *
     * @return bool
     */
    public function getEditTodayAttribute(): bool
    {
        return $this->hasInteraction('edit');
    }

    /**
     * Accesseur pour savoir si l'utilisateur courant a commenter le post aujourd'hui.
     *
     * @return bool
     */
    public function getCommentTodayAttribute(): bool
    {
        return $this->hasInteraction('comment');
    }

    /**
     * Accesseur retournant le chemin relatif vers la vue de l'article.
     *
     * @return string
     */
    public function getRouteAttribute() {
        return $this->rubric->route()."/{$this->id}";
    }

    /**
     * Vérifie si l'utilisateur courant peut commenter cet article.
     *
     * @return bool
     */
    public function isCommentable() {
        return auth()->user()
            ->hasRole('comments', Roles::IS_EDITR, 'Post', $this->id);
    }

    /**
     * Retourne une chaîne descriptive du nombre de commentaires.
     *
     * @return string
     */
    public function commentsInfo() {
        $commentsNb = $this->comments->count();
        $commentsNbLabel = $commentsNb ?: 'Aucun';

        return "{$commentsNbLabel} ".($commentsNb > 1 ? 'commentaires' : 'commentaire');
    }


    /**
     * Vérifie si l'article a été lu par l'utilisateur courant.
     *
     * @return bool
     */
    public function isRead() {
        $postUser = $this->readers->find(auth()->user()->id);

        return $postUser ? $postUser->pivot->is_read : FALSE;
    }

    /**
     * Récupère les tags personnels de l'utilisateur sur cet article.
     *
     * @return string|null
     */
    public function tags() {
        $postUser = $this->readers->find(auth()->user()->id);

        return $postUser ? $postUser->pivot->tags : NULL;
    }

    /**
     * Récupère l'interaction d'acquittement de l'utilisateur courant pour cet article.
     *
     * @return \App\Models\Interaction|null
     */
    public function getAcknowledgment(): ?\App\Models\Interaction
    {
        $user = auth()->user();
        if (! $user) return NULL;

        return $this->interactions()
            ->byUser($user)
            ->ofType('acknowledge')
            ->latest('occurred_at')
            ->first();
    }

    /**
     * Vérifie si l'utilisateur courant a acquitté la lecture de cet article.
     *
     * @return bool
     */
    public function isAcknowledged(): bool
    {
        return $this->getAcknowledgment() !== NULL;
    }

    /**
     * Accesseur vérifiant si l'article est programmé pour le futur.
     *
     * @return bool
     */
    public function getForthcomingAttribute() {
        return isset($this->published_at) && $this->published_at->format('Y-m-d') > today()->format('Y-m-d');
    }

    /**
     * Accesseur vérifiant si l'article a expiré.
     *
     * @return bool
     */
    public function getExpiredAttribute() {
        return isset($this->expired_at) && $this->expired_at->format('Y-m-d') <= today()->format('Y-m-d');
    }

    /**
     * Accesseur vérifiant si l'article est actuellement diffusé.
     *
     * @return bool
     */
    public function getReleasedAttribute() {
        return $this->published && !$this->forthcoming && !$this->expired;
    }

    /**
     * Accesseur vérifiant si l'article est archivé (publié et expiré mais non supprimé).
     *
     * @return bool
     */
    public function getArchivedAttribute() {
        return $this->published && $this->expired && !$this->auto_delete;
    }

    /**
     * Accesseur retournant le statut actuel de l'article sous forme d'objet (icon, title, color).
     *
     * @return object|null
     */
    public function getStatusAttribute() {
        switch (TRUE) {
            case !$this->published : return AP::getPostStatusMI('unpublished');
            case $this->archived : return AP::getPostStatusMI('archived');
            case $this->expired : return AP::getPostStatusMI('expired');
            case $this->forthcoming : return AP::getPostStatusMI('forthcoming');
            case $this->released : return AP::getPostStatusMI('released');
        }
    }

    /**
     * Accesseur retournant le nombre de lecteurs uniques l'ayant lu.
     *
     * @return int
     */
    public function getViewsNbAttribute() {
        return $this->readers()
            ->where('is_read', TRUE)
            ->get()
            ->count();
    }

    /**
     * Accesseur retournant le nombre de commentaires.
     *
     * @return int
     */
    public function getCommentsNbAttribute() {
        return $this->comments
            ->count();
    }

    /**
     * Génère un aperçu textuel du contenu (sans tags HTML).
     *
     * @return string
     */
    public function preview() {
        return AP::strLimiter(strip_tags($this->content));
    }

    /**
     * Génère un aperçu du titre limité en longueur.
     *
     * @return string
     */
    public function previewTitle() {
        return AP::strLimiter(strip_tags($this->title),60);
    }

    /**
     * Retourne l'identité complète de l'article (Titre + Rubrique).
     *
     * @return string
     */
    public function identity() {
        return "{$this->title} ({$this->rubric->name})";
    }

    /**
     * Retourne tous les articles triés par rubrique puis titre.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function sort() {
        return self::query()
            ->orderByRaw('rubric_id ASC, title ASC')
            ->get();
    }

    /**
     * Filtre les articles selon des critères complexes (rubrique, auteur, phase, recherche).
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $posts = static::query()
            ->when($rubricId, function ($query) use ($rubricId) {
                $query->where('rubric_id', $rubricId);
            })
            ->when($authorId, function ($query) use ($authorId) {
                $query->where('author_id', $authorId);
            })
            ->when($phase, function ($query) use ($phase) {
                switch ($phase) {
                    case 'unpublished' :
                        $query
                            ->where('published', FALSE);
                        break;

                    case 'forthcoming' :
                        $query
                            ->where('published_at', '>', today()->format('Y-m-d'));
                        break;

                    case 'released' :
                        $query
                            ->where('published', TRUE)
                            ->where(function ($query) {
                                $query
                                    ->where('published_at', '<=', today()->format('Y-m-d'))
                                    ->orWhereNull('published_at');
                            })
                            ->where(function ($query) {
                                $query
                                    ->where('expired_at', '>', today()->format('Y-m-d'))
                                    ->orWhereNull('expired_at');
                            });
                        break;

                    case 'archived' :
                        $query
                            ->where('expired_at', '<=', today()->format('Y-m-d'))
                            ->where('auto_delete', FALSE);
                        break;

                    case 'expired' :
                        $query
                            ->where('expired_at', '<=', today()->format('Y-m-d'))
                            ->where('auto_delete', TRUE);
                        break;
                }
            })
            ->get()
            ->when($search, function ($posts) use ($search) {
                return $posts->filter(function ($post) use ($search) {
                    return static::tableContains([
                        $post->title,
                        $post->author->identity,
                        $post->status->title,
                    ], $search);
                });
            });

        return $posts->isEmpty() ? static::whereNull('id') : $posts->toQuery();
    }

    /**
     * Récupère tous les articles qui peuvent être commentés globalement.
     * Exclut ceux qui ont une restriction de droit spécifique pour le groupe 'GLOBAL'.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function allCommentable() {
        return static::query()
            ->whereNotIn('id', function ($query) {
                $query->select('resource_id')
                    ->from('rightables')
                    ->join('rights', 'rights.id', '=', 'rightables.right_id')
                    ->where('rights.name', 'comments')
                    ->where('rightables.rightable_type', 'Group')
                    ->where('rightables.rightable_id', Group::where('name', 'GLOBAL')->first()->id)
                    ->where('resource_type', 'Post')
                    ->whereRaw('!(rightables.roles & '.Roles::IS_EDITR.')');
            });
    }
}
