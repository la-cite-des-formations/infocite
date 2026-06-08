<?php

namespace App\Statistics;

use App\Models\User;
use App\Models\Employee;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Statistiques liées aux utilisateurs.
 * Gère les compteurs de notifications, l'activité éditoriale et l'usage des applications personnelles.
 */
class Users
{
    /**
     * Récupère les employés ayant désactivé les notifications de bureau.
     */
    public static function allRefuseDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', FALSE);
    }

    /**
     * Récupère les employés ayant activé toutes les notifications de bureau.
     */
    public static function allGrantAllDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', TRUE)
            ->where('notify_only_favorites', FALSE);
    }

    /**
     * Récupère les employés ayant activé les notifications de bureau uniquement pour les favoris.
     */
    public static function allGrantOnlyFavoritesDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', TRUE)
            ->where('notify_only_favorites', TRUE);
    }

    /**
     * Récupère les éditeurs les plus actifs (création/modification d'articles).
     *
     * @param array $filter
     *      - editorType: 'all' | 'authors' (create) | 'correctors' (update)
     *      - schoolYear: int
     *      - month: int
     *      - rubricId: int|array
     */
    public static function getActiveEditors(array $filter = []) {
        $schoolYear = $filter['schoolYear'] ?? NULL;
        $month      = $filter['month'] ?? NULL;
        $editorType = $filter['editorType'] ?? 'all';
        $rubricId   = $filter['rubricId'] ?? NULL;

        return User::withCount(['postsEditInteractions as posts_count' => function ($q) use ($schoolYear, $month, $editorType, $rubricId) {

            $q->select(DB::raw('COUNT(DISTINCT(target_id))'));

            if ($schoolYear) {
                $start = Carbon::create($schoolYear, 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();
                $q->whereBetween('occurred_at', [$start, $end]);

                if ($month) {
                    $q->whereMonth('occurred_at', $month);
                }
            }

            if ($editorType === 'authors') {
                $q->ofType('create');
            } elseif ($editorType === 'correctors') {
                $q->ofType('update');
            }

            if ($rubricId) {
                $q->whereHasMorph(
                    'target',
                    [Post::class],
                    function ($q2) use ($rubricId) {
                        if (is_array($rubricId)) {
                            $q2->whereIn('rubric_id', $rubricId);
                        } else {
                            $q2->where('rubric_id', $rubricId);
                        }
                    }
                );
            }
        }])
        ->having('posts_count', '>', 0)
        ->orderByDesc('posts_count')
        ->orderBy('name')
        ->orderBy('first_name');
    }

    /**
     * Récupère les commentateurs les plus actifs.
     *
     * @param array $filter
     *      - commentatorType: 'all' | 'staff' | 'learners'
     *      - schoolYear: int
     *      - month: int
     */
    public static function getActiveCommentators(array $filter = []) {
        $schoolYear      = $filter['schoolYear'] ?? NULL;
        $month           = $filter['month'] ?? NULL;
        $commentatorType = $filter['commentatorType'] ?? 'all';

        return User::withCount(['postsCommentInteractions as comments_count' => function ($q) use ($schoolYear, $month, $commentatorType) {

            if ($schoolYear) {
                $start = Carbon::create($schoolYear, 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();
                $q->whereBetween('occurred_at', [$start, $end]);

                if ($month) {
                    $q->whereMonth('occurred_at', $month);
                }
            }

            if ($commentatorType) {
                $q->byUserType($commentatorType);
            }
        }])
        ->having('comments_count', '>', 0)
        ->orderByDesc('comments_count')
        ->orderBy('name')
        ->orderBy('first_name');
    }

    /**
     * Récupère les utilisateurs notant le plus d'articles.
     *
     * Les notes sont stockées dans la table pivot post_user (colonne rating).
     * Pas de filtre par date (les notes ne sont pas horodatées).
     * Tri : nombre d'articles notés décroissant, puis nom/prénom alphabétique.
     *
     * Compatible MySQL ONLY_FULL_GROUP_BY : l'agrégation est faite dans une sous-requête
     * sur post_user, puis jointe sur users pour récupérer le modèle complet.
     *
     * @param array $filter
     *      - raterType: 'all' | 'staff' | 'learners'
     */
    public static function getActiveRaters(array $filter = []) {
        $raterType = $filter['raterType'] ?? 'all';

        // Sous-requête : agrégation sur post_user uniquement
        $ratingsSubQuery = DB::table('post_user')
            ->join('users as u', 'u.id', '=', 'post_user.user_id')
            ->where('post_user.rating', '>', 0)
            ->when($raterType && $raterType !== 'all', function ($q) use ($raterType) {
                $q->where('u.is_staff', $raterType === 'staff');
            })
            ->select(
                'post_user.user_id',
                DB::raw('COUNT(post_user.post_id) as ratings_count'),
                DB::raw('ROUND(AVG(post_user.rating), 2) as ratings_avg')
            )
            ->groupBy('post_user.user_id');

        // Jointure avec le modèle User — pas de GROUP BY sur users
        return User::query()
            ->joinSub($ratingsSubQuery, 'ratings', function ($join) {
                $join->on('users.id', '=', 'ratings.user_id');
            })
            ->when($raterType && $raterType !== 'all', function ($q) use ($raterType) {
                $q->where('users.is_staff', $raterType === 'staff');
            })
            ->select('users.*', 'ratings.ratings_count', 'ratings.ratings_avg')
            ->orderByDesc('ratings.ratings_count')
            ->orderBy('users.name')
            ->orderBy('users.first_name');
    }

    /**
     * Statistiques sur les utilisateurs créant des applications personnelles.
     *
     * @param array $filter
     *      - userType: 'all' | 'staff' | 'learners'
     */
    public static function personalAppsUsers($filter = []) {
        extract($filter);
        $byStaff = !isset($userType) || ($userType == 'all') ? NULL : $userType == 'staff';

        return User::query()
            ->join('apps', 'apps.owner_id', '=', 'users.id')
            ->selectRaw('users.name, users.first_name, COUNT(*) AS apps_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query->where('users.is_staff', $byStaff);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('apps_nb DESC, users.name, users.first_name');
    }
}
