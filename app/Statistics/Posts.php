<?php

namespace App\Statistics;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Posts
{
    /**
     * Récupère les articles vus selon les filtres.
     *
     * @param array $filter
     *      - readerType: 'all' | 'staff' | 'learners'
     *      - rubricId: int|array|null
     *      - schoolYear: int|null
     *      - month: int|null
     */
    public static function getViewed(array $filter = []) {
        return Post::withCount(['viewInteractions as views_count' => function ($q) use ($filter) {

            if (!empty($filter['schoolYear'])) {
                $start = Carbon::create($filter['schoolYear'], 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();
                $q->whereBetween('occurred_at', [$start, $end]);

                if (!empty($filter['month'])) {
                    $q->whereMonth('occurred_at', $filter['month']);
                }
            }

            if (!empty($filter['readerType'])) {
                $q->byUserType($filter['readerType']);
            }

            if (!empty($filter['rubricId'])) {
                if (is_array($filter['rubricId'])) {
                    $q->whereIn('rubric_id', $filter['rubricId']);
                } else {
                    $q->where('rubric_id', $filter['rubricId']);
                }
            }
        }])
        ->having('views_count', '>', 0)
        ->orderByDesc('views_count')
        ->orderBy('title');
    }

    /**
     * Récupère les articles commentés selon les filtres.
     *
     * @param array $filter
     *      - readerType: 'all' | 'staff' | 'learners'
     *      - rubricId: int|array|null
     *      - schoolYear: int|null
     *      - month: int|null
     */
    public static function getCommented(array $filter = []) {
        return Post::withCount(['commentInteractions as comments_count' => function ($q) use ($filter) {

            if (!empty($filter['schoolYear'])) {
                $start = Carbon::create($filter['schoolYear'], 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();
                $q->whereBetween('occurred_at', [$start, $end]);

                if (!empty($filter['month'])) {
                    $q->whereMonth('occurred_at', $filter['month']);
                }
            }

            if (!empty($filter['readerType'])) {
                $q->byUserType($filter['readerType']);
            }

            if (!empty($filter['rubricId'])) {
                if (is_array($filter['rubricId'])) {
                    $q->whereIn('rubric_id', $filter['rubricId']);
                } else {
                    $q->where('rubric_id', $filter['rubricId']);
                }
            }
        }])
        ->having('comments_count', '>', 0)
        ->orderByDesc('comments_count')
        ->orderBy('title');
    }

    /**
     * Récupère les articles les mieux notés.
     *
     * Les notes sont stockées dans la table pivot post_user (colonne rating).
     * Pas de filtre par date (les notes ne sont pas horodatées).
     * Tri : note moyenne décroissante, puis nombre de notes décroissant (ex-æquo), puis titre.
     *
     * Compatible MySQL ONLY_FULL_GROUP_BY : l'agrégation est faite dans une sous-requête
     * sur post_user, puis jointe sur posts pour récupérer le modèle complet.
     *
     * @param array $filter
     *      - readerType: 'all' | 'staff' | 'learners'
     *      - rubricId: int|array|null
     */
    public static function getRated(array $filter = []) {
        $readerType = $filter['readerType'] ?? 'all';
        $rubricId   = $filter['rubricId'] ?? [];

        // Sous-requête : agrégation sur post_user uniquement
        $ratingsSubQuery = DB::table('post_user')
            ->join('users', 'users.id', '=', 'post_user.user_id')
            ->where('post_user.rating', '>', 0)
            ->when($readerType && $readerType !== 'all', function ($q) use ($readerType) {
                $q->where('users.is_staff', $readerType === 'staff');
            })
            ->select(
                'post_user.post_id',
                DB::raw('ROUND(AVG(post_user.rating), 2) as ratings_avg'),
                DB::raw('COUNT(post_user.user_id) as ratings_count')
            )
            ->groupBy('post_user.post_id');

        // Jointure avec le modèle Post — pas de GROUP BY sur posts
        return Post::query()
            ->joinSub($ratingsSubQuery, 'ratings', function ($join) {
                $join->on('posts.id', '=', 'ratings.post_id');
            })
            ->when(!empty($rubricId), function ($q) use ($rubricId) {
                if (is_array($rubricId)) {
                    $q->whereIn('posts.rubric_id', $rubricId);
                } else {
                    $q->where('posts.rubric_id', $rubricId);
                }
            })
            ->select('posts.*', 'ratings.ratings_avg', 'ratings.ratings_count')
            ->orderByDesc('ratings.ratings_avg')
            ->orderByDesc('ratings.ratings_count')
            ->orderBy('posts.title');
    }
}
