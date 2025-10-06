<?php

namespace App\Charts;

use App\Statistics\Connections;
use App\Statistics\Posts;
use App\Statistics\Users;
use Carbon\Carbon;

class Charts
{
    private static function buildTop10Chart($items, $valueKey, $tooltipBuilder) {
        return [
            'cols' => [
                ['label' => 'Label', 'type' => 'string'],
                ['label' => 'Value', 'type' => 'number'],
                ['role' => 'tooltip', 'type' => 'string', 'p' => ['html' => true]],
            ],
            'rows' => $items->take(10)->map(function ($item, $rank) use ($valueKey, $tooltipBuilder) {
                return [
                    'c' => [
                        ['v' => $rank < 3 ? 'Top ' . ($rank + 1) : ''],
                        ['v' => $item->{$valueKey}],
                        ['v' => $tooltipBuilder($item, $rank)],
                    ],
                ];
            }),
        ];
    }

    public static function getConnectionsByDayChart($filter) {
        $data = Connections::groupByLastTwoWeeks($filter);

        $rows = [];
        $daysOfWeek = collect(range(0, 6))->map(fn($i) => now()->startOfWeek()->addDays($i)->locale('fr')->dayName);

        foreach ($daysOfWeek as $dayName) {
            $datePrev = now()->startOfWeek()->subWeek()->addDays($daysOfWeek->search($dayName))->toDateString();
            $dateCurr = now()->startOfWeek()->addDays($daysOfWeek->search($dayName))->toDateString();

            $rows[] = [
                'c' => [
                    ['v' => $dayName],
                    ['v' => $data['previous'][$datePrev]->connections_nb ?? 0],
                    ['v' => $data['current'][$dateCurr]->connections_nb ?? 0],
                ]
            ];
        }

        return [
            'cols' => [
                ['label' => 'Jour de la semaine', 'type' => 'string'],
                ['label' => 'Semaine dernière', 'type' => 'number'],
                ['label' => 'Semaine actuelle', 'type' => 'number'],
            ],
            'rows' => $rows
        ];
    }

    public static function getConnectionsByMonthChart(array $filter = []) {
        $data = Connections::groupBySchoolYearAndMonth($filter);

        $today = now();
        $startSchoolYear  = $today->month >= 9 ? $today->year : $today->year - 1;

        $rows = [];

        // Année scolaire = septembre → août
        $start = Carbon::create($startSchoolYear, 9, 1)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);

            $keyCurr = $month->format('Y-m');                // ex: 2024-09
            $keyPrev = $month->copy()->subYear()->format('Y-m'); // ex: 2023-09

            $monthName = ucfirst($month->locale('fr')->monthName);

            $rows[] = [
                'c' => [
                    ['v' => $monthName],
                    ['v' => $data['previous'][$keyPrev] ?? 0],
                    ['v' => $data['current'][$keyCurr] ?? 0],
                ]
            ];
        }

        return [
            'cols' => [
                ['label' => 'Mois', 'type' => 'string'],
                ['label' => "Année dernière " . ($startSchoolYear - 1) . '-' . substr($startSchoolYear, 2), 'type' => 'number'],
                ['label' => "Année actuelle " . ($startSchoolYear) . '-' . substr($startSchoolYear + 1, 2),  'type' => 'number'],
            ],
            'rows' => $rows,
        ];
    }

    public static function getNotificationsUseChart() {
        return [
            'cols' => [
                ['label' => 'Statut de notification', 'type' => 'string'],
                ['label' => "Nombre d'utilisateurs", 'type' => 'number'],
            ],
            'rows' => [
                [
                    'c' => [
                        ['v' => 'Aucune'],
                        ['v' => Users::allRefuseDesktopNotifications()->get()->count()],
                    ]
                ],
                [
                    'c' => [
                        ['v' => 'Toutes'],
                        ['v' => Users::allGrantAllDesktopNotifications()->get()->count()],
                    ]
                ],
                [
                    'c' => [
                        ['v' => 'Favoris'],
                        ['v' => Users::allGrantOnlyFavoritesDesktopNotifications()->get()->count()],
                    ]
                ],
            ]
        ];
    }

    public static function getViewedPostsTop10Chart($filter) {
        $posts = Posts::getViewed($filter)->get();

        return self::buildTop10Chart(
            $posts,
            'views_count',
            function ($post, $rank) {
                return
                    "<div class='w-100 m-3' style='max-width: 400px;'>
                        <h6 class='fw-bold'>" . ($rank + 1) . " - {$post->title}</h6>
                        <ul>
                            <li>Rubrique - {$post->rubric->name}</li>
                            <li>{$post->views_count} vues</li>
                        </ul>
                    </div>";
            }
        );
    }

    public static function getCommentedPostsTop10Chart($filter) {
        $posts = Posts::getCommented($filter)->get();

        return self::buildTop10Chart(
            $posts,
            'comments_count',
            function ($post, $rank) {
                return
                    "<div class='w-100 m-3' style='max-width: 400px;'>
                        <h6 class='fw-bold'>" . ($rank + 1) . " - {$post->title}</h6>
                        <ul>
                            <li>Rubrique - {$post->rubric->name}</li>
                            <li>{$post->comments_count} commentaires</li>
                        </ul>
                    </div>";
            }
        );
    }

    public static function getActiveEditorsTop10Chart($filter) {
        $editors = Users::getActiveEditors($filter)->get();

        return self::buildTop10Chart(
            $editors,
            'posts_count',
            function ($editor, $rank) {
                return
                    "<div class='w-100 m-3' style='max-width: 400px;'>
                        <h6 class='fw-bold'>".($rank + 1)." - {$editor->identity}</h6>
                        <ul>
                            <li>{$editor->posts_count} articles</li>
                        </ul>
                    </div>";
            }
        );
    }

    public static function getActiveCommentatorsTop10Chart($filter) {
        $commentators = Users::getActiveCommentators($filter)->get();

        return self::buildTop10Chart(
            $commentators,
            'comments_count',
            function ($commentator, $rank) {
                return
                    "<div class='w-100 m-3' style='max-width: 400px;'>
                        <h6 class='fw-bold'>".($rank + 1)." - {$commentator->identity}</h6>
                        <ul>
                            <li>{$commentator->comments_count} commentaires</li>
                        </ul>
                    </div>";
            }
        );
    }

    public static function getPersonalAppsUsersTop10Chart($filter) {
        $users = Users::personalAppsUsers($filter)->get();

        return self::buildTop10Chart(
            $users,
            'apps_nb',
            function ($user, $rank) {
                return
                    "<div class='w-100 m-3' style='max-width: 400px;'>
                        <h6 class='fw-bold'>".($rank + 1)." - {$user->identity}</h6>
                        <ul>
                            <li>{$user->apps_nb} applications personnelles</li>
                        </ul>
                    </div>";
            }
        );
    }

    public static function getChartOptions($chartName) {
        switch ($chartName) {
            case 'connectionsByDay' :
            case 'connectionsByMonth' :
                return [
                    'colors' => ['#9ec5fe', '#0d6efd'],
                    'backgroundColor' => '#f8fafc',
                    'chartArea' => [
                        'backgroundColor' => '#f8fafc',
                    ],
                ];

            case 'notificationsUse' :
                return [
                    'colors' => ['#0d6efd'],
                    'backgroundColor' => '#f8fafc',
                    'chartArea' => [
                        'backgroundColor' => '#f8fafc',
                    ],
                ];

            case 'viewedPostsTop10' :
            case 'commentedPostsTop10' :
            case 'activeEditorsTop10' :
            case 'activeCommentatorsTop10' :
            case 'personalAppsUsersTop10' :
                return [
                    'pieHole' => 0.4,
                    'pieSliceText' => 'label',
                    'tooltip' => [
                        'text' => 'value',
                        'isHtml' => TRUE,
                        'ignoreBounds' => TRUE,
                        'trigger' => 'selection',
                    ],
                    'legend' => [
                        'position' => 'none',
                    ],
                    'backgroundColor' => '#f8fafc',
                    'chartArea' => [
                        'backgroundColor' => '#f8fafc',
                    ],
                ];
        }
    }

    public static function getChart($chartName, $filter) {
        $functionName = "get" . ucfirst($chartName) . "Chart";

        return static::$functionName($filter);
    }
}
