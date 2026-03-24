<?php

namespace App\Models;

use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use App\Models\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Représente un utilisateur du système.
 *
 * Cette classe gère les différentes facettes d'un utilisateur :
 * - Ses rôles et permissions (droits personnels, par profil ou par groupe).
 * - Ses relations avec les autres entités (Apprenant, Employé, Rubriques, etc.).
 * - Ses applications personnelles et favorites.
 * - Ses interactions et notifications au sein de la plateforme.
 */
class User extends Authenticatable
{
    use WithSearching;
    use Notifiable;
    use HasPermissions;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'first_name', 'email', 'password', ];

    /**
     * Les attributs qui ne sont pas assignables en masse.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Les attributs qui doivent être cachés pour les tableaux/JSON.
     *
     * @var array<string>
     */
    protected $hidden = ['remember_token', ];

    /**
     * Les attributs qui doivent être castés dans des types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'account_expires_on' => 'date:Y-m-d',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation vers le profil Apprenant de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function learner()
    {
        return $this
            ->hasOne(Learner::class);
    }

    /**
     * Relation vers le profil Employé de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this
            ->hasOne(Employee::class);
    }

    /**
     * Relation vers les jetons FCM (Firebase Cloud Messaging) de l'utilisateur.
     * Utilisé pour les notifications push.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fcmTokens() {
        return $this->belongsToMany(FcmToken::class);
    }

    /**
     * Relation vers l'entité Actor correspondante.
     * L'ID de l'acteur est identique à l'ID de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor() {
        return $this
            ->belongsTo(Actor::class, 'id');
    }

    /**
     * Relation vers toutes les interactions effectuées par l'utilisateur.
     * Triées par date d'occurrence décroissante.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function interactions() {
        return $this
            ->hasMany(Interaction::class)
            ->orderBy('occurred_at', 'desc');
    }

    /**
     * Filtre les interactions de création d'articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postsCreateInteractions() {
        return $this
            ->interactions()
            ->ofType('create')
            ->withTargetType(Post::class);
    }

    /**
     * Filtre les interactions de mise à jour d'articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postsUpdateInteractions() {
        return $this
            ->interactions()
            ->ofType('update')
            ->withTargetType(Post::class);
    }

    /**
     * Filtre les interactions d'édition (création ou mise à jour) d'articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postsEditInteractions() {
        return $this
            ->interactions()
            ->ofTypes(['create', 'update'])
            ->withTargetType(Post::class);
    }

    /**
     * Filtre les interactions de commentaire sur des articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postsCommentInteractions() {
        return $this
            ->interactions()
            ->ofType('comment')
            ->withTargetType(Post::class);
    }

    /**
     * Relation vers les articles créés par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function myPosts() {
        return $this
            ->hasMany(Post::class, 'author_id')
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * Relation vers les articles lus par l'utilisateur.
     * Inclut les informations de pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postsRead() {
        return $this
            ->belongsToMany(Post::class)
            ->withPivot(['is_read'])
            ->where('is_read', TRUE)
            ->orderBy('created_at', 'DESC');
    }

    /**
     * Relation vers les articles mis à jour par l'utilisateur (en tant que correcteur).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedPosts() {
        return $this
            ->hasMany(Post::class, 'corrector_id')
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * Relation vers les articles commentés par l'utilisateur.
     * Utilise une relation traversante via le modèle Comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function commentedPosts() {
        return $this->hasManyThrough(Post::class, Comment::class, 'user_id', 'id', 'id', 'post_id')
            ->distinct()
            ->orderBy('created_at', 'desc');
    }


    /**
     * Relation vers les rubriques mises en favoris par l'utilisateur.
     * Table 'favorites' (polymorphique).
     */
    public function favoriteRubrics() {
        return $this->morphedByMany(Rubric::class, 'favoriteable', 'favorites')
                    ->withPivot('rank');
    }

    /**
     * Relation vers les articles mis en favoris par l'utilisateur.
     * Table 'favorites' (polymorphique).
     */
    public function favoritePosts() {
        return $this->morphedByMany(Post::class, 'favoriteable', 'favorites')
                    ->withPivot('rank');
    }

    /**
     * Relation vers les applications mises en favoris par l'utilisateur.
     * Table 'favorites' (polymorphique).
     */
    public function favoriteApps() {
        return $this->morphedByMany(App::class, 'favoriteable', 'favorites')
                    ->withPivot('rank')
                    ->orderByRaw('rank ASC');
    }

    /**
     * Récupère toutes les notifications liées aux rubriques et articles favoris de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myNotifications() {
        $myNotifications = new Collection();

        $this->favoriteRubrics->each(function ($rubric) use (&$myNotifications) {
            $rubric->posts->each(function ($post) use (&$myNotifications) {
                $myNotifications = $myNotifications->merge($post->notifications);
            });
        });

        $this->favoritePosts->each(function ($post) use (&$myNotifications) {
            $myNotifications = $myNotifications->merge($post->notifications);
        });

        return $myNotifications;
    }

    /**
     * Récupère les notifications déjà consultées (rejetées de la liste des nouvelles).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function oldNotifications() {
        return $this->myNotifications()->reject(function ($notification) {
            return $this->newNotifications->contains('id', $notification->id);
        });
    }

    /**
     * Relation vers les nouvelles notifications non lues de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function newNotifications() {
        return $this
            ->belongsToMany(Notification::class);
    }

    /**
     * Relation vers les commentaires rédigés par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function myComments() {
        return $this
            ->hasMany(Comment::class)
            ->orderBy('created_at', 'DESC');
    }


    /**
     * Récupère l'ensemble des rubriques accessibles par l'utilisateur.
     * Inclut ses propres rubriques, celles de ses groupes et de ses profils.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myRubrics() {
        $myRubrics = new Collection();

        $this->groups->each(function ($group) use (&$myRubrics) {
            $myRubrics = $myRubrics->merge($group->rubrics);
        });

        $this->profiles->each(function ($profile) use (&$myRubrics) {
            $myRubrics = $myRubrics->merge($profile->myRubrics());
        });

        return $myRubrics->unique();
    }

    /**
     * Relation vers les profils associés à l'utilisateur.
     * Un utilisateur peut hériter des droits de plusieurs profils.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles() {
        return $this
            ->belongsToMany(self::class, 'profile_user', 'user_id', 'profile_id')
            ->orderByRaw('name ASC, first_name ASC')
            ->withTimestamps();
    }

    /**
     * Relation inverse : utilisateurs appartenant à ce profil.
     * (Pertinent si l'instance actuelle est un profil).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this
            ->belongsToMany(self::class, 'profile_user', 'profile_id', 'user_id')
            ->orderByRaw('name ASC, first_name ASC')
            ->withTimestamps();
    }

    /**
     * Relation vers les applications accessibles par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps() {
        return $this
            ->belongsToMany(App::class)
            ->orderByRaw('name ASC')
            ->withPivot(['login', 'password']);
    }

    /**
     * Relation vers les applications personnelles créées par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personnalApps() {
        return $this
            ->hasMany(App::class, 'owner_id')
            ->orderByRaw('name ASC');
    }

    /**
     * Récupère toutes les applications disponibles (directes, groupes, profils, favorites).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myApps() {
        $myApps = $this->apps;

        $this->groups->each(function ($group) use (&$myApps) {
            $myApps = $myApps->merge($group->apps);
        });

        $this->profiles->each(function ($profile) use (&$myApps) {
            $myApps = $myApps->merge($profile->myApps());
        });

        return $this->favoriteApps->merge($myApps->isEmpty() ?
            $myApps :
            $myApps
                ->sortBy('name')
        );
    }


    /**
     * Récupère les processus métiers associés à l'utilisateur via ses groupes de type 'P'.
     *
     * @return \Illuminate\Support\Collection
     */
    public function processes() {
        return $this
            ->groups(['P'])->get()
            ->filter(function ($group) {
                return is_object($group->process);
            })
            ->pluck('process');
    }

    /**
     * Relation vers les subordonnés (si l'utilisateur est un manager).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function subordinates() {
        return $this
            ->hasManyThrough(self::class, Actor::class, 'manager_id', 'id', 'id', 'id')
            ->orderByRaw('name ASC, first_name ASC');
    }

    /**
     * Relation vers le manager de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function manager() {
        return $this
            ->hasOneThrough(self::class, Actor::class, 'id', 'id', 'id', 'manager_id');
    }

    /**
     * Relation vers les groupes auxquels appartient l'utilisateur.
     *
     * @param array|null $types Filtrer par types de groupe (ex: ['P', 'E', 'C']).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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

    /**
     * Récupère tous les groupes (directs et via profils).
     *
     * @param array|null $types Filtrer par types de groupe.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myGroups(? array $types = NULL)
    {
        $myGroups = $this->groups($types)->get();

        $this->profiles->each(function ($profile) use (&$myGroups, $types) {
            $myGroups = $myGroups->merge($profile->myGroups($types));
        });

        return $myGroups;
    }

    /**
     * Relation vers tous les numéros de téléphone de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Récupère un numéro de téléphone d'un type spécifique.
     *
     * @param string $type Type de téléphone (ex: 'Portable').
     * @return Phone|null
     */
    public function phone($type) {
        return $this->phones->firstWhere('type', $type);
    }

    /**
     * Retourne une liste formatée (chaîne de caractères) des noms de groupes.
     *
     * @param array|null $types Types de groupes à inclure.
     * @param string $format Format de chaîne (utilise %% comme placeholder).
     * @param string $noResult Valeur si aucun groupe n'est trouvé.
     * @return string
     */
    public function groupsList(? array $types = NULL, string $format = "%%", string $noResult = '')
    {
        $result = $this
            ->myGroups($types)
            ->pluck('name')
            ->implode(', ');

        return $result ? str_replace("%%", $result, $format) : $noResult;
    }

    /**
     * Retourne une liste formatée des processus associés.
     *
     * @param string $format Format de chaîne.
     * @param string $noResult Valeur si aucun processus n'est trouvé.
     * @return string
     */
    public function processesList(string $format = "%%", string $noResult = '')
    {
        $result = $this
            ->processes()
            ->pluck('name')
            ->implode(', ');

        return $result ? str_replace("%%", $result, $format) : $noResult;
    }



    /**
     * Retourne la fonction de l'utilisateur dans un groupe spécifique.
     *
     * @param int $groupId ID du groupe.
     * @param string $format Format de sortie.
     * @param string $noResult Valeur par défaut.
     * @return string
     */
    public function function(int $groupId, string $format = "%%", string $noResult = '') {
        $result = $this
            ->groups
            ->find($groupId)
            ->pivot->function;

        return isset($result) ? str_replace("%%", $result, $format) : $noResult;
    }

    /**
     * Retourne une liste formatée des fonctions au sein de certains types de groupes.
     *
     * @param array $types Types de groupes.
     * @param string $format Format de sortie.
     * @param string $noResult Valeur par défaut.
     * @return string
     */
    public function functionsList(array $types, string $format = "%%", string $noResult = '') {
        $result = $this
            ->groups($types)
            ->whereNotNull('function')
            ->pluck('function')
            ->implode(', ');

        return $result ? str_replace("%%", $result, $format) : $noResult;
    }



    /**
     * Vérifie si l'utilisateur est présent dans l'organigramme en tant que manager.
     *
     * @return bool
     */
    public function isManager() {
        $isManager = FALSE;

        Chartnode::all()->each(function ($chartnode) use(&$isManager) {
            if ($this->id == $chartnode->manager_id) {
                return $isManager = TRUE;
            }
        });

        return $isManager;
    }

    /**
     * Vérifie si l'utilisateur est un simple "profil" (modèle de droits).
     *
     * @return bool
     */
    public function isProfile() {
        return $this->name == AP::PROFILE;
    }

    /**
     * Retourne l'identité complète (Prénom Nom ou Nom du profil).
     *
     * @return string
     */
    public function identity() {
        return $this->isProfile() ?
            "{$this->first_name} (profil)" :
            "{$this->first_name} {$this->name}";
    }

    /**
     * Accesseur pour l'attribut identity.
     *
     * @return string
     */
    public function getIdentityAttribute() {
        return $this->isProfile() ?
            "{$this->first_name} (profil)" :
            "{$this->first_name} {$this->name}";
    }

    /**
     * Accesseur pour récupérer le label référent associé.
     *
     * @return string
     */
    public function getLabelAttribute() {
        $referent = DB::table('referents')
            ->where('id', $this->id)
            ->first();

        return is_object($referent) ? $referent->label : '';
    }

    /**
     * Accesseur pour l'identité affichée dans l'organigramme.
     *
     * @return string
     */
    public function getChartnodeIdentityAttribute() {
        return $this->identity.AP::betweenBrackets($this->label ?: '');
    }

    /**
     * Accesseur pour récupérer le nom du processus associé via le code fonction Ypareo.
     *
     * @return string
     */
    public function getProcessAttribute() {
        $processUser = Chartnode::query()
            ->where('code_fonction', $this->groups(['P'])->first()->code_ypareo)
            ->first();

        return is_object($processUser) ? $processUser->name : '';
    }

    /**
     * Vérifie si l'utilisateur s'est connecté aujourd'hui.
     *
     * @return bool
     */
    public function getConnectedTodaydAttribute() {
        return Interaction::ofType('connection')
            ->byUser(auth()->user())
            ->forDate(today())
            ->exists();
    }

    /**
     * Accesseur retournant le nombre total de messages édités (créés + corrigés).
     *
     * @return int
     */
    public function getEditedPostsNbAttribute() {
        return $this->myPosts
            ->merge($this->updatedPosts)
            ->count();
    }

    /**
     * Accesseur retournant le nombre total de messages commentés.
     *
     * @return int
     */
    public function getCommentedPostsNbAttribute() {
        return $this->commentedPosts()
            ->get()
            ->count();
    }

    /**
     * Accesseur retournant le nombre d'applications personnelles.
     *
     * @return int
     */
    public function getPersonalAppsNbAttribute() {
        return $this->personnalApps
            ->count();
    }

    /**
     * Accesseur retournant le statut des notifications de bureau.
     *
     * @return string Libellé explicite de l'état (Aucune, Toutes, Favoris, Non disponibles).
     */
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

    /**
     * Récupère une information textuelle spécifique basée sur un en-tête (Statut, Classe, Service, Fonction).
     *
     * @param array $userInfo Contient notamment 'header', 'groupType', 'groupId'.
     * @return string|null
     */
    public function getInfo($userInfo) {
        extract($userInfo);

        switch (TRUE) {
            case $header === 'Statut' && $this->is_staff :
                return "Personnel{$this->groupsList(['P'], ' (%%)')}";

            case $header === 'Statut' && !$this->is_staff :
                return "Apprenant{$this->groupsList(['C'], ' (%%)')}";

            case $header === 'Classe' :
            case $header === 'Groupes' :
                return $this->groupsList([$groupType], '%%', '-');

            case $header === 'Service' :
                return $this->groupsList(['E']);

            case $header === 'Fonction' && (!$groupId || ($groupId && $groupType === 'F')):
                return  $this->groupsList(['E']).($this->functionsList(['E'], " (%%)") ?: $this->groupsList(['P'], ' (%%)'));

            case $header === 'Fonction' && $groupId && $groupType === 'E':
                return $this->functionsList(['E']) ?: $this->groupsList(['P']);

            case $header === 'Fonction' && $groupId && $groupType !== 'E':
                return $this->functionsList(['C'], '%%', '-');

        }
    }

    /**
     * Retourne tous les utilisateurs triés par Nom puis Prénom.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function sort() {
        return self::query()
            ->orderByRaw('name ASC, first_name ASC')
            ->get();
    }

    /**
     * Filtre les utilisateurs selon un ensemble de critères (recherche, types de groupes, état gelé).
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

}
