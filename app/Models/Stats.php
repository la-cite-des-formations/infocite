<?php

namespace App\Models;

use Illuminate\Support\Collection;

class Stats
{
    public static function getConnectionsChart($filter) {
        $connectionsByDay = new Collection();
        $today = today();
        $date = $today->subDays(6 + $today->dayOfWeek ?: 7);

        for ($i = 0; $i < 14; $i++) {
            $connectionsByDay->push([
                'dayName' => $date->locale('fr')->dayName,
                'connectionsNb' => Connection::fromDate($date, $filter)->get()->count()
            ]);
            $date->addDays(1);
        }

        for ($i = 0; $i < 7; $i++) {
            $rows[$i] = [
                'c' => [
                    ['v' => $connectionsByDay[$i]['dayName']],
                    ['v' => $connectionsByDay[$i]['connectionsNb']],
                    ['v' => $connectionsByDay[$i + 7]['connectionsNb']],
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

    public static function getTopTenViewedPostsChart($filter) {
        return [
            'cols' => [
                ['label' => 'Article', 'type' => 'string'],
                ['label' => 'Nombre de vues', 'type' => 'number'],
                ['role' => 'tooltip', 'type' => 'string', 'p' => ['html' => TRUE]]
            ],
            'rows' => Post::allViewed($filter)->take(10)->get()->map(function ($post, $rank) {
                return [
                    'c' => [
                        ['v' => $rank < 3 ? 'Top '.($rank + 1) : ''],
                        ['v' => $post->views_nb],
                        ['v' => "<div class='w-100 m-3' style='max-width: 400px;'>
                            <h6 class='fw-bold'>".($rank + 1)." - {$post->title}</h6>
                            <ul>
                                <li>Rubrique - {$post->rubric}</li>
                                <li>{$post->views_nb} vues</li>
                            </ul>
                        </div>"]
                    ]
                ];
            })
        ];
    }

    public static function getTopTenCommentedPostsChart($filter) {
        return [
            'cols' => [
                ['label' => 'Article', 'type' => 'string'],
                ['label' => 'Nombre de commentaires', 'type' => 'number'],
                ['role' => 'tooltip', 'type' => 'string', 'p' => ['html' => TRUE]]
            ],
            'rows' => Post::allCommented($filter)->take(10)->get()->map(function ($post, $rank) {
                return [
                    'c' => [
                        ['v' => $rank < 3 ? 'Top '.($rank + 1) : ''],
                        ['v' => $post->comments_nb],
                        ['v' => "<div class='w-100 m-3' style='max-width: 400px;'>
                            <h6 class='fw-bold'>".($rank + 1)." - {$post->title}</h6>
                            <ul>
                                <li>Rubrique - {$post->rubric}</li>
                                <li>{$post->comments_nb} commentaires</li>
                            </ul>
                        </div>"]
                    ]
                ];
            })
        ];
    }

    public static function getActiveEditorsTop10Chart($filter) {
        return [
            'cols' => [
                ['label' => 'Rédacteur', 'type' => 'string'],
                ['label' => "Nombre d'articles'", 'type' => 'number'],
                ['role' => 'tooltip', 'type' => 'string', 'p' => ['html' => TRUE]]
            ],
            'rows' => User::activeEditors($filter)->take(10)->get()->map(function ($editor, $rank) {
                return [
                    'c' => [
                        ['v' => $rank < 3 ? 'Top '.($rank + 1) : ''],
                        ['v' => $editor->posts_nb],
                        ['v' => "<div class='w-100 m-3' style='max-width: 400px;'>
                            <h6 class='fw-bold'>".($rank + 1)." - {$editor->identity}</h6>
                            <ul>
                                <li>{$editor->posts_nb} articles</li>
                            </ul>
                        </div>"]
                    ]
                ];
            })
        ];
    }

    public static function getActiveCommentatorsTop10Chart($filter) {
        return [
            'cols' => [
                ['label' => 'Commentateur', 'type' => 'string'],
                ['label' => 'Nombre de commentaires', 'type' => 'number'],
                ['role' => 'tooltip', 'type' => 'string', 'p' => ['html' => TRUE]]
            ],
            'rows' => User::activeCommentators($filter)->take(10)->get()->map(function ($commentator, $rank) {
                return [
                    'c' => [
                        ['v' => $rank < 3 ? 'Top '.($rank + 1) : ''],
                        ['v' => $commentator->comments_nb],
                        ['v' => "<div class='w-100 m-3' style='max-width: 400px;'>
                            <h6 class='fw-bold'>".($rank + 1)." - {$commentator->identity}</h6>
                            <ul>
                                <li>{$commentator->comments_nb} commentaires</li>
                            </ul>
                        </div>"]
                    ]
                ];
            })
        ];
    }

    public static function getPersonalAppsUsersTop10Chart($filter) {
        return [
            'cols' => [
                ['label' => 'Utilisateur', 'type' => 'string'],
                ['label' => "Nombre d'applications personnelles", 'type' => 'number'],
                ['role' => 'tooltip', 'type' => 'string', 'p' => ['html' => TRUE]]
            ],
            'rows' => User::personalAppsUsers($filter)->take(10)->get()->map(function ($user, $rank) {
                return [
                    'c' => [
                        ['v' => $rank < 3 ? 'Top '.($rank + 1) : ''],
                        ['v' => $user->apps_nb],
                        ['v' => "<div class='w-100 m-3' style='max-width: 400px;'>
                            <h6 class='fw-bold'>".($rank + 1)." - {$user->identity}</h6>
                            <ul>
                                <li>{$user->apps_nb} applications personnelles</li>
                            </ul>
                        </div>"]
                    ]
                ];
            })
        ];
    }

    public static function getChartOptions($chartName) {
        switch ($chartName) {
            case 'connections' :
                return [
                    'colors' => ['#9ec5fe', '#0d6efd'],
                    'backgroundColor' => '#f8fafc',
                    'chartArea' => [
                        'backgroundColor' => '#f8fafc',
                    ],
                ];

            case 'topTenViewedPosts' :
            case 'topTenCommentedPosts' :
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
        switch ($chartName) {
            case 'connections' :
                return static::getConnectionsChart($filter);

            case 'topTenViewedPosts' :
                return static::getTopTenViewedPostsChart($filter);

            case 'topTenCommentedPosts' :
                return static::getTopTenCommentedPostsChart($filter);

            case 'activeEditorsTop10' :
                return static::getActiveEditorsTop10Chart($filter);

            case 'activeCommentatorsTop10' :
                return static::getActiveCommentatorsTop10Chart($filter);

            case 'personalAppsUsersTop10' :
                return static::getPersonalAppsUsersTop10Chart($filter);
        }
    }
}
