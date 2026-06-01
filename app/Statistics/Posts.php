<?php

namespace App\Statistics;

use App\Models\Post;
use Carbon\Carbon;

class Posts
{
    /**
     * Récupère les articles vus selon les filtres
     *
     * @param array $filter
     *      - readerType: 'all' | 'staff' | 'learners'
     *      - rubricId: int|null
     *      - schoolYear: int|null
     *      - month: int|null
     */
    public static function getViewed(array $filter = []) {
        return Post::withCount(['viewInteractions as views_count' => function ($q) use ($filter) {

            // Filtre par année scolaire
            if (!empty($filter['schoolYear'])) {
                $start = Carbon::create($filter['schoolYear'], 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();

                $q->whereBetween('occurred_at', [$start, $end]);

                // Filtre par mois uniquement si année scolaire précisée
                if (!empty($filter['month'])) {
                    $q->whereMonth('occurred_at', $filter['month']);
                }
            }
            // Filtre par type de lecteur
            if (!empty($filter['readerType'])) {
                $q->byUserType($filter['readerType']);
            }

            // Filtre par rubrique
            if (!empty($filter['rubricId'])) {
                $q->where('rubric_id', $filter['rubricId']);
            }
        }])
        ->having('views_count', '>', 0)
        ->orderByDesc('views_count')
        ->orderBy('title');
    }

    /**
     * Récupère les articles commentés selon les filtres
     *
     * @param array $filter
     *      - readerType: 'all' | 'staff' | 'learners'
     *      - rubric_id: int|null
     *      - schoolYear: int|null
     *      - month: int|null
     */
    public static function getCommented(array $filter = []) {
        return Post::withCount(['commentInteractions as comments_count' => function ($q) use ($filter) {

            // Filtre par année scolaire
            if (!empty($filter['schoolYear'])) {
                $start = Carbon::create($filter['schoolYear'], 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();

                $q->whereBetween('occurred_at', [$start, $end]);

                // Filtre par mois uniquement si année scolaire précisée
                if (!empty($filter['month'])) {
                    $q->whereMonth('occurred_at', $filter['month']);
                }
            }

            // Filtre par type de lecteur
            if (!empty($filter['readerType'])) {
                $q->byUserType($filter['readerType']);
            }

            // Filtre par rubrique
            if (!empty($filter['rubricId'])) {
                $q->where('rubric_id', $filter['rubricId']);
            }
        }])
        ->having('comments_count', '>', 0)
        ->orderByDesc('comments_count')
        ->orderBy('title');
    }
}
