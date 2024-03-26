<?php

namespace App\CustomFacades;

use App\Right;
use App\Roles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

//use Illuminate\Support\Facades\DB;

class AP // Application Parameters
{
    static private $miCodes = NULL;

    const COOKIE_LIFETIME = 60 * 24 * 60; // 60 jours * 24 heures * 60 minutes
    const STRICTLY = FALSE;
    const PROFILE = '@PROFILE';
    const ID_REGEX = '[0-9]+';
    const RUBRIC_SEPARATOR = '.';
    const RUBRIC_REGEX = '[-\w'.self::RUBRIC_SEPARATOR.']+';
    const SUBDASHBOARD_REGEX = '[-\w]+';
    const RUBRIC_POSITIONS = [
        'N' => 'Barre de navigation',
        'F' => 'Pied de page',
        'U' => 'Position indéfinie',
    ];

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
            'actors' => ['name' => 'org-chart'],
            'chartnodes' => ['name' => 'org-chart'],
        ],
    ];

    const RESOURCEABLES = [
        'App' => 'Application',
        'Group' => 'Groupe',
        'Post' => 'Article',
        'Rubric' => 'Rubrique',
        'User' => 'Utilisateur',
    ];

    const APP_AUTH_TYPES = [
        'N' => 'Sans authentification',
        'G' => 'Authentification via Google',
        'S' => 'Authentification spécifique',
    ];

    const APP_TYPES = [
        'P' => 'Applications personnelles',
        'I' => 'Applications institutionnelles',
    ];

    const QUALITIES = [
        'E' => 'Externe',
        'I' => 'Interne',
        'D' => 'Demi-pensionnaire',
    ];

    const GROUP_TYPES = [
        'C' => 'Classe apprenants',
        'E' => 'Classe formateurs',
        'P' => 'Fonction / Processus',
        'S' => 'Système',
        'A' => 'Autre',
    ];

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
                'icon' => 'corporate_fare',
                'header' => 'Service'
            ],
            'withGroupId' => [
                'icon' => 'build',
                'header' => 'Fonction'
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
        'E' => [
            'icon' => 'school',
            'choiceLabel' => 'Choisir une classe...',
            'dtLabel' => 'Classes'
        ],
        'P' => [
            'icon' => 'corporate_fare',
            'choiceLabel' => 'Choisir un service...',
            'dtLabel' => 'Processus'
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
            'data' => [
                'title' => 'Données',
                'table_title' => 'Gestion des données',
                'description' => "Gérer les données via différents scripts",
                'icon_name' => 'perm_data_setting',
                'color' => 'danger',
                'gate' => 'manage-data',
                'route' => NULL
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
                'title' => 'Noeuds graphiques',
                'table_title' => 'Gestion des noeuds graphiques',
                'description' => "Gérer les noeuds graphiques",
                'icon_name' => 'developer_board',
                'color' => 'orange',
                'gate' => 'manage-chartnodes',
                'route' => ['name' => 'admin.chartnodes.index', 'parameters' => NULL]
            ],
            'actors' => [
                'title' => 'Hiérarchie',
                'table_title' => 'Gestion des liens hiérarchiques',
                'description' => "Gérer les liens hiérarchiques",
                'icon_name' => 'supervisor_account',
                'color' => 'orange',
                'gate' => 'manage-actors',
                'route' => ['name' => 'admin.actors.index', 'parameters' => NULL]
            ],
        ],
    ];

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

    const NOTIFICATIONS = [
        'NP' => 'Nouvel article disponible : ',
        'UP' => 'Article mis à jour le @date : ',
        'CP' => 'Article commenté : ',
        'NA' => 'Nouvelle application disponible : ',
        'UA' => 'Application mise à jour : ',
        'UO' => 'Organigramme mis à jour : ',
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

    public static function getResourceables() {
        return static::RESOURCEABLES;
    }

    public static function getResourceable($resourceType) {
        return static::RESOURCEABLES[$resourceType];
    }

    public static function getRubricPositions() {
        return static::RUBRIC_POSITIONS;
    }

    public static function getRubricPosition($position) {
        return static::RUBRIC_POSITIONS[$position];
    }

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

    public static function getModels($dashboard = '') {
        return array_keys(static::getModelsRights($dashboard));
    }

    public static function getModelRight($model) {
        $modelRight = (object) static::getModelsRights()[$model];
        $right = Right::where('name', $modelRight->name)->first();
        $modelRight->roles = $right ? $right->dashboard_roles : Roles::NONE;

        return $modelRight;
    }

    public static function getQuality($qualityCode) {
        return static::QUALITIES[$qualityCode];
    }

    public static function getGroupType($groupTypeCode) {
        return static::GROUP_TYPES[$groupTypeCode];
    }

    public static function getAppAuthType($appAuthTypeCode) {
        return static::APP_AUTH_TYPES[$appAuthTypeCode];
    }

    public static function getUserInfo($groupType, $groupId) {
        return $groupId ?
            static::USER_INFO[$groupType]['withGroupId'] :
            static::USER_INFO[$groupType]['withoutGroupId'];
    }

    public static function getGroupFilter($groupType = '') {
        return static::GROUP_FILTER[$groupType];
    }

    public static function getGroupTypes() {
        return static::GROUP_TYPES;
    }

    public static function getAppAuthTypes() {
        return static::APP_AUTH_TYPES;
    }

    public static function getAppTypes() {
        return static::APP_TYPES;
    }

    public static function getDashboardFunctions($dashboard = 'main') {
        return new Collection(array_map(function ($function) { return (object) $function; }, static::DASHBOARD_FUNCTIONS[$dashboard]));
    }

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

    public static function getDashboardFunction($function, $dashboard = 'main') {
        return static::getDashboardFunctions($dashboard)
            ->get($function);
    }

    public static function getMedia($media) {
        return (object) static::MEDIAS[$media];
    }

    public static function getMedias() {
        return array_map(function ($media) { return (object) $media; }, static::MEDIAS);
    }

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

    public static function getMiCodes() {
        return static::$miCodes ?? static::$miCodes = new Collection(json_decode(file_get_contents('../resources/mi_codepoints.json'), TRUE));
    }

    public static function getMiCode($miName) {
        return isset(static::$miCodes) ? static::$miCodes[$miName] : static::getMiCodes()[$miName];
    }

    public static function getRecentMiCodes() {
        return new Collection(json_decode(Cookie::get('recentMiCodes'), TRUE));
    }

    public static function getPostStatus() {
        return array_map(
            function ($postStatus) {
                return (object) $postStatus;
            },
            static::POST_STATUS_MI
        );
    }

    public static function getPostStatusMI($status) {
        return (object) static::POST_STATUS_MI[$status];
    }

    public static function getBorderStyles() {
        return static::BORDER_STYLES;
    }

    private static function toColor($n)
    {
        return "#".substr("000000".dechex($n), -6);
    }

    private static function toAlpha($n)
    {
        return substr("00".dechex(round((1 + $n) * 0xff)), -2);
    }

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

    public static function getFormatBgColors() {
        return array_map(function ($color) {
            return
                'background: none '.static::gradeColor($color, -0.8).'; '.
                'border-color: '.static::gradeColor($color, 0.8).'; '.
                'color: '.static::gradeColor($color, 0.4);
        }, static::BS_COLORS);
    }

    public static function getNotifications($contentType) {
        return static::NOTIFICATIONS[$contentType];
    }

    public static function betweenBrackets($str, $withSpace = TRUE) {
        return !empty($str) ? ($withSpace ? ' ' : '')."({$str})" : '';
    }
}
