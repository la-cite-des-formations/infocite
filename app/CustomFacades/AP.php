<?php

namespace App\CustomFacades;

use App\Models\Right;
use App\Models\Roles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

//use Illuminate\Support\Facades\DB;

/**
 * Classe centrale (Application Parameters) contenant les constantes, paramètres de configuration
 * et méthodes utilitaires statiques pour l'ensemble du portail.
 * Utilisée via la façade 'AP'.
 */
class AP // Application Parameters
{
    /** @var \Illuminate\Support\Collection|null Cache pour les codes d'icônes Material Design. */
    static private $miCodes = NULL;

    /** @var int Durée de vie par défaut des cookies (60 jours). */
    const COOKIE_LIFETIME = 60 * 24 * 60; // 60 jours * 24 heures * 60 minutes
    /** @var bool Définit si les contrôles de rôles doivent être stricts par défaut. */
    const STRICTLY = FALSE;
    /** @var string Marqueur pour identifier les profils utilisateurs dans la base. */
    const PROFILE = '@PROFILE';
    /** @var string Regex pour valider un ID numérique. */
    const ID_REGEX = '[0-9]+';
    /** @var string Séparateur utilisé dans les chemins de rubriques. */
    const RUBRIC_SEPARATOR = '.';
    /** @var string Regex pour valider un segment de rubrique. */
    const RUBRIC_REGEX = '[-\w'.self::RUBRIC_SEPARATOR.']+';
    /** @var string Regex pour valider un segment de sous-tableau de bord. */
    const SUBDASHBOARD_REGEX = '[-\w]+';
    /** @var array<string, string> Libellés des positions possibles pour les rubriques. */
    const RUBRIC_POSITIONS = [
        'N' => 'Barre de navigation',
        'F' => 'Pied de page',
        'U' => 'Position indéfinie',
    ];

    /**
     * Configuration des droits par défaut pour les différents modèles gérés dans les tableaux de bord.
     * Associe chaque modèle à un 'droit' (Right) et définit éventuellement des droits additionnels.
     *
     * @var array
     */
    const DASHBOARD_MODELS_RIGHTS = [
        'main' => [
            'apps' => ['name' => 'apps'],
            'data' => ['name' => 'data'],
            'groups' => ['name' => 'groups'],
            'posts' => ['name' => 'posts'],
            'comments' => ['name' => 'comments'],
            'profiles' => ['name' => 'profiles'],
            'rights' => [
                'name' => 'rights',
                'others' => [
                    ['name' => 'users', 'roles' => Roles::IS_ADMIN],
                    ['name' => 'profiles', 'roles' => Roles::IS_ADMIN],
                    ['name' => 'groups', 'roles' => Roles::IS_ADMIN],
                ]
            ],
            'rubrics' => ['name' => 'rubrics'],
            'users' => ['name' => 'users'],
        ],
        'org-chart' => [
            'formats' => ['name' => 'org-chart'],
            'chartnodes' => ['name' => 'org-chart'],
            'labels' => ['name' => 'org-chart'],
            //'actors' => ['name' => 'org-chart'],
        ],
        'stats' => [
            'connections' => ['name' => 'stats'],
            'viewing' => ['name' => 'stats'],
            'using' => ['name' => 'stats'],
        ],
    ];

    /** @var array<string, string> Liste des types de ressources pouvant avoir des droits associés. */
    const RESOURCEABLES = [
        'App' => 'Application',
        'Group' => 'Groupe',
        'Post' => 'Article',
        'Rubric' => 'Rubrique',
        'User' => 'Utilisateur',
    ];

    /** @var array<string, string> Types d'authentification supportés pour les applications. */
    const APP_AUTH_TYPES = [
        'N' => 'Sans authentification',
        'G' => 'Authentification via Google',
        'S' => 'Authentification spécifique',
    ];

    /** @var array<string, string> Catégories d'applications (Personnelles vs Institutionnelles). */
    const APP_TYPES = [
        'P' => 'Applications personnelles',
        'I' => 'Applications institutionnelles',
    ];

    /** @var array<string, string> Qualités/Statuts des employés ou usagers. */
    const QUALITIES = [
        'E' => 'Externe',
        'I' => 'Interne',
        'D' => 'Demi-pensionnaire',
    ];

    /** @var array<string, string> Types de groupes d'utilisateurs. */
    const GROUP_TYPES = [
        'C' => 'Classe apprenants',
        'F' => 'Classe formateurs',
        'E' => 'Equipe Pédagogique',
        'P' => 'Fonction (Processus)',
        'S' => 'Système',
        'A' => 'Autre',
    ];

    /**
     * Configuration de l'affichage des informations utilisateur selon le type de groupe.
     * Définit l'icône et le libellé de l'en-tête.
     *
     * @var array
     */
    const USER_INFO = [
        '' => [
            'withoutGroupId' => [
                'icon' => 'attribution',
                'header' => 'Statut'
            ],
        ],
        'C' => [
            'withoutGroupId' => [
                'icon' => 'school',
                'header' => 'Classe'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
        ],
        'F' => [
            'withoutGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
        ],
        'E' => [
            'withoutGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
        ],
        'P' => [
            'withoutGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
            'withGroupId' => [
                'icon' => 'corporate_fare',
                'header' => 'Service'
            ],
        ],
        'P+' => [
            'withoutGroupId' => [
                'icon' => 'developer_board',
                'header' => 'Processus'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
        ],
        'S' => [
            'withoutGroupId' => [
                'icon' => 'groups',
                'header' => 'Groupes'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
        ],
        'A' => [
            'withoutGroupId' => [
                'icon' => 'groups',
                'header' => 'Groupe'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
            ],
        ],
    ];

    /**
     * Configuration des filtres de groupes pour l'interface utilisateur.
     *
     * @var array
     */
    const GROUP_FILTER = [
        '' => [
            'icon' => 'attribution',
            'choiceLabel' => '...'
        ],
        'C' => [
            'icon' => 'school',
            'choiceLabel' => 'Choisir une classe...',
            'dtLabel' => 'Classes'
        ],
        'F' => [
            'icon' => 'school',
            'choiceLabel' => 'Choisir une classe...',
            'dtLabel' => 'Classes'
        ],
        'E' => [
            'icon' => 'corporate_fare',
            'choiceLabel' => 'Choisir un service...',
            'dtLabel' => 'Equipes pédagogiques'
        ],
        'P' => [
            'icon' => 'build',
            'choiceLabel' => 'Choisir une fonction...',
            'dtLabel' => 'Fonctions'
        ],
        'S' => [
            'icon' => 'groups',
            'choiceLabel' => 'Choisir un groupe...',
            'dtLabel' => 'Groupes Système'
        ],
        'A' => [
            'icon' => 'groups',
            'choiceLabel' => 'Choisir un groupe...',
            'dtLabel' => 'Autres groupes'
        ],
        'P+' => [
            'icon' => 'developer_board',
            'choiceLabel' => 'Choisir un processus...'
        ],
    ];

    /**
     * Filtres disponibles pour la page "À la Une".
     *
     * @var array
     */
    const UNE_FILTER = [
        'allPosts'=>
            [
                'name'=>'allPosts',
                'libelle'=>'Articles publiés',
                'icone'=>'article',
            ],
        'notViewPosts' =>
            [
                'name'=>'notViewPosts',
                'libelle'=>'Articles non consultés',
                'icone'=>'fiber_new',
            ],
        'notAcknowledgedPosts' =>
            [
                'name'=>'notAcknowledgedPosts',
                'libelle'=>'Articles non acquittés',
                'icone'=>'draw',
            ],
        'favoritePosts'=>
            [
                'name'=>'favoritePosts',
                'libelle'=>'Articles favoris',
                'icone'=>'favorite',
            ],
        'postsInFavoritesRubrics'=>
            [
                'name'=>'postsInFavoritesRubrics',
                'libelle'=>'Articles des rubriques favorites',
                'icone'=>'favorite_border',
            ],
    ];

    /**
     * Options de tri pour la page "À la Une".
     *
     * @var array
     */
    const UNE_SORTER = [
        'mostConsultedPosts' =>
            [
                'name'=>'mostConsultedPosts',
                'libelle'=>'Articles les plus consultés',
                'icone'=>'remove_red_eye',
            ],
        'mostRecentlyPosts' =>
            [
                'name'=>'mostRecentlyPosts',
                'libelle'=>'Articles les plus récents',
                'icone'=>'access_time',
            ],
        'mostCommentedPosts' =>
            [
                'name'=>'mostCommentedPosts',
                'libelle'=>'Articles les plus commentés',
                'icone'=>'auto_awesome_motion',
            ],
    ];

    /**
     * Définition exhaustive des fonctions disponibles dans les différents tableaux de bord.
     * Contient les titres, descriptions, icônes, droits d'accès et routes cibles.
     *
     * @var array
     */
    const DASHBOARD_FUNCTIONS = [
        'main' => [
            'users' => [
                'title' => 'Utilisateurs',
                'table_title' => 'Gestion des utilisateurs',
                'description' => "Consulter, ajouter ou gérer des utilisateurs",
                'icon_name' => 'manage_accounts',
                'color' => 'primary',
                'gate' => 'manage-users',
                'route' => ['name' => 'admin.users.index', 'parameters' => NULL]
            ],
            'profiles' => [
                'title' => 'Profils',
                'table_title' => 'Gestion des profils utilisateurs',
                'description' => "Ajouter ou gérer des profils utilisateurs",
                'icon_name' => 'portrait',
                'color' => 'primary',
                'gate' => 'manage-profiles',
                'route' => ['name' => 'admin.profiles.index', 'parameters' => NULL]
            ],
            'groups' => [
                'title' => 'Groupes',
                'table_title' => 'Gestion des groupes',
                'description' => "Gérer les groupes d'utilisateurs",
                'icon_name' => 'groups',
                'color' => 'indigo',
                'gate' => 'manage-groups',
                'route' => ['name' => 'admin.groups.index', 'parameters' => NULL]
            ],
            'apps' => [
                'title' => 'Applications',
                'table_title' => 'Gestion des applications',
                'description' => "Gérer les applications et leur paramètres",
                'icon_name' => 'apps',
                'color' => 'purple',
                'gate' => 'manage-apps',
                'route' => ['name' => 'admin.apps.index', 'parameters' => NULL]
            ],
            'org-chart' => [
                'title' => 'Organigramme',
                'table_title' => NULL,
                'description' => "Gérer l'organigramme dynamique",
                'icon_name' => 'lan',
                'color' => 'orange',
                'gate' => ['name' => 'access-dashboard', 'dashboard' => 'org-chart'],
                'route' => ['name' => 'dashboard.sub-dashboard', 'parameters' => ['org-chart']]
            ],
            'rubrics' => [
                'title' => 'Rubriques',
                'table_title' => 'Gestion des rubriques',
                'description' => "Ajouter ou gérer des rubriques",
                'icon_name' => 'menu',
                'color' => 'teal',
                'gate' => 'manage-rubrics',
                'route' => ['name' => 'admin.rubrics.index', 'parameters' => NULL]
            ],
            'posts' => [
                'title' => 'Contenus',
                'table_title' => 'Gestion des contenus',
                'description' => "Ajouter ou gérer des contenus d'information",
                'icon_name' => 'article',
                'color' => 'success',
                'gate' => 'manage-posts',
                'route' => ['name' => 'admin.posts.index', 'parameters' => NULL]
            ],
            'comments' => [
                'title' => 'Commentaires',
                'table_title' => 'Gestion des commentaires',
                'description' => "Voir ou supprimer des commentaires",
                'icon_name' => 'comment',
                'color' => 'success',
                'gate' => 'manage-comments',
                'route' => ['name' => 'admin.comments.index', 'parameters' => NULL]
            ],
            'rights' => [
                'title' => 'Droits',
                'table_title' => 'Gestion des droits utilisateur',
                'description' => "Gérer et appliquer les droits utilisateurs",
                'icon_name' => 'key',
                'color' => 'danger',
                'gate' => 'manage-rights',
                'route' => ['name' => 'admin.rights.index', 'parameters' => NULL]
            ],
            'stats' => [
                'title' => 'Statistiques',
                'table_title' => NULL,
                'description' => "Consulter les statistiques",
                'icon_name' => 'poll',
                'color' => 'danger',
                'gate' => ['name' => 'access-dashboard', 'dashboard' => 'stats'],
                'route' => ['name' => 'dashboard.sub-dashboard', 'parameters' => ['stats']]
            ],
        ],
        'org-chart' => [
            'formats' => [
                'title' => 'Mise en forme',
                'table_title' => 'Gestion des mises en forme',
                'description' => "Gérer la mise en forme de l'organigramme",
                'icon_name' => 'format_shapes',
                'color' => 'orange',
                'gate' => 'manage-formats',
                'route' => ['name' => 'admin.formats.index', 'parameters' => NULL]
            ],
            'chartnodes' => [
                'title' => 'Nœuds ',
                'table_title' => "Gestion des nœuds graphiques",
                'description' => "Gérer les nœuds graphiques",
                'icon_name' => 'pages',
                'color' => 'orange',
                'gate' => 'manage-chartnodes',
                'route' => ['name' => 'admin.chartnodes.index', 'parameters' => NULL]
            ],
            'labels' => [
                'title' => 'Libellés ',
                'table_title' => "Consultation des libellés référents",
                'description' => "Consulter les libellés référents",
                'icon_name' => 'local_offer',
                'color' => 'orange',
                'gate' => 'manage-labels',
                'route' => ['name' => 'admin.labels.index', 'parameters' => NULL]
            ],
            // 'actors' => [
            //     'title' => 'Hiérarchie',
            //     'table_title' => 'Gestion des liens hiérarchiques',
            //     'description' => "Gérer les liens hiérarchiques",
            //     'icon_name' => 'supervisor_account',
            //     'color' => 'orange',
            //     'gate' => 'manage-actors',
            //     'route' => ['name' => 'admin.actors.index', 'parameters' => NULL]
            // ],
        ],
        'stats' => [
            'connections' => [
                'title' => 'Connexions ',
                'table_title' => "Consultation des connexions",
                'description' => "Statistiques concernant les connexions",
                'icon_name' => 'hub',
                'color' => 'danger',
                'gate' => 'manage-connections',
                'route' => ['name' => 'admin.connections.index', 'parameters' => NULL]
            ],
            'viewing' => [
                'title' => 'Articles ',
                'table_title' => "Statistiques concernant les articles",
                'description' => "Statistiques concernant les articles",
                'icon_name' => 'auto_stories',
                'color' => 'danger',
                'gate' => 'manage-viewing',
                'route' => ['name' => 'admin.viewing.index', 'parameters' => NULL]
            ],
            'using' => [
                'title' => 'Usage ',
                'table_title' => "Statistiques concernant l'utilisation",
                'description' => "Statistiques concernant l'utilisation",
                'icon_name' => 'surfing',
                'color' => 'danger',
                'gate' => 'manage-using',
                'route' => ['name' => 'admin.using.index', 'parameters' => NULL]
            ],
        ],
    ];

    /** @var array<string, array> Réseaux sociaux et liens externes institutionnels. */
    const MEDIAS = [
        'facebook' => [
            'title' => "Suivez-nous sur Facebook",
            'iconClass' => "bx bxl-facebook",
            'url' => "https://www.facebook.com/lacitedesformations"
        ],
        'instagram' => [
            'title' => "Rejoignez-nous sur Instagram",
            'iconClass' => "bx bxl-instagram",
            'url' => "https://www.instagram.com/lacitedesformations"
        ],
        'youtube' => [
            'title' => "Visionnez les vidéos de notre chaîne YouTube",
            'iconClass' => "bx bxl-youtube",
            'url' => "https://www.youtube.com/user/CFAdeTours"
        ],
        'linkedin' => [
            'title' => 'Découvrez-nous sur Linkedin',
            'iconClass' => "bx bxl-linkedin",
            'url' => "https://www.linkedin.com/company/25028580"
        ],
    ];

    const BORDER_STYLES = [
        'continu' => 'solid',
        'pointillé' => 'dotted',
    ];

    const BS_COLORS = [
        'primary' => 0x0d6efd,
        'secondary' => 0x6c757d,
        'success' => 0x198754,
        'info' => 0x0dcaf0,
        'warning' => 0xffc107,
        'danger' => 0xdc3545,
        'light' => 0xf8f9fa,
        'dark' => 0x212529,
    ];

    const GC_COLORS = [
        0x3366cc,
        0xdc3912,
        0xff9900,
        0x109618,
        0x990099,
        0x0099c6,
        0xdd4477,
        0x66aa00,
        0xb82e2e,
        0x316395,
    ];

    const POST_STATUS_MI = [
        'released' => ['icon' => 'check_circle', 'title' => "Actuel"],
        'unpublished' => ['icon' => 'unpublished', 'title' => "Non publié"],
        'expired' => ['icon' => 'auto_delete', 'title' => "Auto-supprimé"],
        'archived' => ['icon' => 'inventory_2', 'title' => "Archivé"],
        'forthcoming' => ['icon' => 'schedule_send', 'title' => "À venir"],
    ];

    /*public static function getFieldEnum($table, $field) {
        return explode(
            ',',
            preg_replace(
                "/[^A-Z,]/",
                '',
                DB::select("SHOW COLUMNS FROM $table WHERE Field = ?", [$field])[0]->Type
            )
        );
    }*/

    /**
     * Retourne la liste des types de ressources configurables.
     *
     * @return array<string, string>
     */
    public static function getResourceables() {
        return static::RESOURCEABLES;
    }

    /**
     * Retourne le libellé d'un type de ressource spécifique.
     *
     * @param string $resourceType Le code de la ressource.
     * @return string
     */
    public static function getResourceable($resourceType) {
        return static::RESOURCEABLES[$resourceType];
    }

    /**
     * Retourne les positions possibles pour les rubriques.
     *
     * @return array<string, string>
     */
    public static function getRubricPositions() {
        return static::RUBRIC_POSITIONS;
    }

    /**
     * Retourne le libellé d'une position de rubrique.
     *
     * @param string $position Le code de la position.
     * @return string
     */
    public static function getRubricPosition($position) {
        return static::RUBRIC_POSITIONS[$position];
    }

    /**
     * Retourne les droits configurés pour les modèles, soit globalement, soit pour un tableau de bord spécifique.
     *
     * @param string $dashboard Le nom du tableau de bord (ex: 'main', 'org-chart', 'stats').
     * @return array
     */
    public static function getModelsRights($dashboard = '') {
        if (empty($dashboard)) {
            $modelsRights = [];

            foreach(static::DASHBOARD_MODELS_RIGHTS as $dashboard) {
                $modelsRights = array_merge($modelsRights, $dashboard);
            }
            return $modelsRights;
        }
        return static::DASHBOARD_MODELS_RIGHTS[$dashboard];
    }

    /**
     * Retourne la liste des noms de modèles gérés pour un tableau de bord.
     *
     * @param string $dashboard Le nom du tableau de bord.
     * @return string[]
     */
    public static function getModels($dashboard = '') {
        return array_keys(static::getModelsRights($dashboard));
    }

    /**
     * Récupère l'objet de configuration des droits pour un modèle donné.
     * Fusionne la configuration statique avec les rôles stockés en base de données.
     *
     * @param string $model Le nom du modèle.
     * @return object
     */
    public static function getModelRight($model) {
        $modelRight = (object) static::getModelsRights()[$model];
        $right = Right::where('name', $modelRight->name)->first();
        $modelRight->roles = $right ? $right->dashboard_roles : Roles::NONE;

        return $modelRight;
    }

    /**
     * Retourne le libellé associé à un code de qualité.
     *
     * @param string $qualityCode
     * @return string
     */
    public static function getQuality($qualityCode) {
        return static::QUALITIES[$qualityCode];
    }

    /**
     * Retourne le libellé associé à un code de type de groupe.
     *
     * @param string $groupTypeCode
     * @return string
     */
    public static function getGroupType($groupTypeCode) {
        return static::GROUP_TYPES[$groupTypeCode];
    }

    /**
     * Retourne le libellé associé à un code de type d'authentification d'application.
     *
     * @param string $appAuthTypeCode
     * @return string
     */
    public static function getAppAuthType($appAuthTypeCode) {
        return static::APP_AUTH_TYPES[$appAuthTypeCode];
    }

    /**
     * Prépare les paramètres d'icône et d'en-tête pour l'affichage des infos utilisateur.
     *
     * @param array $filter Tableau contenant 'groupType' et 'groupId'.
     * @return array
     */
    public static function getUserInfoParams($filter) {
        return array_merge(
            $filter,
            $filter['groupId'] ?
                static::USER_INFO[$filter['groupType']]['withGroupId'] :
                static::USER_INFO[$filter['groupType']]['withoutGroupId']
        );
    }

    /**
     * Retourne la configuration du filtre pour un type de groupe.
     *
     * @param string $groupType
     * @return array
     */
    public static function getGroupFilter($groupType = '') {
        return static::GROUP_FILTER[$groupType];
    }

    /**
     * Retourne tous les types de groupes définis.
     *
     * @return array<string, string>
     */
    public static function getGroupTypes() {
        return static::GROUP_TYPES;
    }

    /**
     * Retourne tous les types d'authentification d'applications.
     *
     * @return array<string, string>
     */
    public static function getAppAuthTypes() {
        return static::APP_AUTH_TYPES;
    }

    /**
     * Retourne tous les types d'applications.
     *
     * @return array<string, string>
     */
    public static function getAppTypes() {
        return static::APP_TYPES;
    }

    /**
     * Retourne les fonctions d'un tableau de bord sous forme de Collection d'objets.
     *
     * @param string $dashboard Nom du tableau de bord.
     * @return \Illuminate\Support\Collection
     */
    public static function getDashboardFunctions($dashboard = 'main') {
        return new Collection(array_map(function ($function) { return (object) $function; }, static::DASHBOARD_FUNCTIONS[$dashboard]));
    }

    /**
     * Retourne toutes les fonctions de tous les tableaux de bord.
     * Utile pour la recherche globale ou la construction de menus complexes.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllDashboardsFunctions() {
        $allDashboardsfunctions = new Collection();

        foreach (static::getDashboardFunctions() as $function) {

            $function->atRoot = TRUE;
            $allDashboardsfunctions->push($function);

            if (is_array($function->gate)) {
                $allDashboardsfunctions = $allDashboardsfunctions->concat(static::getDashboardFunctions($function->gate['dashboard']));
            }
        }

        return $allDashboardsfunctions;
    }

    /**
     * Retourne une fonction spécifique d'un tableau de bord.
     *
     * @param string $function Nom de la fonction.
     * @param string $dashboard Nom du tableau de bord.
     * @return object|null
     */
    public static function getDashboardFunction($function, $dashboard = 'main') {
        return static::getDashboardFunctions($dashboard)
            ->get($function);
    }

    /**
     * Retourne la configuration d'un média social spécifique.
     *
     * @param string $media Nom du média.
     * @return object
     */
    public static function getMedia($media) {
        return (object) static::MEDIAS[$media];
    }

    /**
     * Retourne tous les médias sociaux configurés.
     *
     * @return object[]
     */
    public static function getMedias() {
        return array_map(function ($media) { return (object) $media; }, static::MEDIAS);
    }

    /**
     * Limite la longueur d'une chaîne de caractères sans couper les mots.
     *
     * @param string $str La chaîne à traiter.
     * @param int $limit Limite de caractères.
     * @param string $addChars Caractères supplémentaires à considérer comme faisant partie des mots.
     * @return string
     */
    public static function strLimiter($str, $limit = 50, $addChars = '0..9') {
        if (strlen($str) > $limit) {
            $words = str_word_count($str, 2, $addChars);
            $length = $limit;
            foreach ($words as $pos => $word) {
                if ($pos + strlen($word) <= $limit) $length = $pos + strlen($word);
                else break;
            }
            $str = substr($str, 0, $length).'...';
        }
        return $str;
    }

    /**
     * Récupère la collection des codes de points (SVG/Icons) Material Design depuis le fichier JSON.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getMiCodes() {
        return static::$miCodes ?? static::$miCodes = new Collection(json_decode(file_get_contents('../resources/mi_codepoints.json'), TRUE));
    }

    /**
     * Retourne le code d'une icône spécifique.
     *
     * @param string $miName Nom de l'icône.
     * @return string|null
     */
    public static function getMiCode($miName) {
        return isset(static::$miCodes) ? static::$miCodes[$miName] : static::getMiCodes()[$miName];
    }

    /**
     * Récupère les codes d'icônes récemment utilisés depuis les cookies.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getRecentMiCodes() {
        return new Collection(json_decode(Cookie::get('recentMiCodes'), TRUE));
    }

    /**
     * Retourne tous les statuts possibles pour les articles.
     *
     * @return object[]
     */
    public static function getPostStatus() {
        return array_map(
            function ($postStatus) {
                return (object) $postStatus;
            },
            static::POST_STATUS_MI
        );
    }

    /**
     * Retourne la configuration d'un statut d'article spécifique.
     *
     * @param string $status Le code du statut.
     * @return object
     */
    public static function getPostStatusMI($status) {
        return (object) static::POST_STATUS_MI[$status];
    }

    /**
     * Retourne les styles de bordure disponibles.
     *
     * @return array<string, string>
     */
    public static function getBorderStyles() {
        return static::BORDER_STYLES;
    }

    /**
     * Convertit un nombre entier en code couleur hexadécimal.
     *
     * @param int $n
     * @return string
     */
    private static function toColor($n)
    {
        return "#".substr("000000".dechex($n), -6);
    }

    /**
     * Convertit une valeur d'opacité en composante hexadécimale alpha.
     *
     * @param float $n
     * @return string
     */
    private static function toAlpha($n)
    {
        return substr("00".dechex(round((1 + $n) * 0xff)), -2);
    }

    /**
     * Applique une variation de teinte (plus clair ou plus foncé) à une couleur.
     *
     * @param int $color La couleur de base.
     * @param float $n Facteur de variation (-1 à 1).
     * @return string
     */
    private static function gradeColor($color, $n) {
        $r = 0xff0000;
        $g = 0x00ff00;
        $b = 0x0000ff;

        if ($n > 0) {
            return static::toColor(
                (($n * ($color & $r)) & $r) |
                (($n * ($color & $g)) & $g) |
                (($n * ($color & $b)) & $b)
            );
        }

        if ($n < 0) {
            return static::toColor(
                (((1 + $n) * ($color & $r) - $n * $r) & $r) |
                (((1 + $n) * ($color & $g) - $n * $g) & $g) |
                (((1 + $n) * ($color & $b) - $n * $b) & $b)
            );
        }

        return $color;
    }

    /**
     * Génère les styles CSS de background et bordure basés sur les couleurs Bootstrap.
     *
     * @return array<string, string>
     */
    public static function getFormatBgColors() {
        return array_map(function ($color) {
            return
                'background: none '.static::gradeColor($color, -0.8).'; '.
                'border-color: '.static::gradeColor($color, 0.8).'; '.
                'color: '.static::gradeColor($color, 0.4);
        }, static::BS_COLORS);
    }

    /**
     * Retourne les couleurs de graphiques au format hexadécimal.
     *
     * @return string[]
     */
    public static function getGcColors() {
        return array_map(function ($color) {
            return static::toColor($color);
        }, static::GC_COLORS);
    }

    /**
     * Entoure une chaîne de caractères de parenthèses si elle n'est pas vide.
     *
     * @param string $str La chaîne.
     * @param bool $withSpace Ajouter une espace avant.
     * @return string
     */
    public static function betweenBrackets($str, $withSpace = TRUE) {
        return !empty($str) ? ($withSpace ? ' ' : '')."({$str})" : '';
    }

    /**
     * Retourne tous les filtres disponibles pour "À la Une".
     *
     * @return array
     */
    public static function getUneFiltered()
    {
        return static::UNE_FILTER;
    }
    /**
     * Retourne un filtre "À la Une" spécifique par son nom.
     *
     * @param string $filterName
     * @return array
     */
    public static function getUneFilteredByName($filterName)
    {
        return static::UNE_FILTER[$filterName];
    }

    /**
     * Retourne toutes les options de tri disponibles pour "À la Une".
     *
     * @return array
     */
    public static function getUneSorted()
    {
        return static::UNE_SORTER;
    }
    /**
     * Retourne une option de tri "À la Une" spécifique par son nom.
     *
     * @param string $sorterName
     * @return array
     */
    public static function getUneSortedByName($sorterName)
    {
        return static::UNE_SORTER[$sorterName];
    }

}
