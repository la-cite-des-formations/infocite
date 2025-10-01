<?php

namespace App\Statistics;

use App\Models\User;
use App\Models\Employee;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Users
{
    public static function allRefuseDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', FALSE);
    }

    public static function allGrantAllDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', TRUE)
            ->where('notify_only_favorites', FALSE);
    }

    public static function allGrantOnlyFavoritesDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', TRUE)
            ->where('notify_only_favorites', TRUE);
    }

    /**
     * Récupère les éditeurs actifs (auteurs/correcteurs)
     *
     * @param array $filter
     *      - editorType: 'all' | 'authors' | 'correctors'
     *      - schoolYear: int|null
     *      - month: int|null
     */
    public static function getActiveEditors(array $filter = []) {
        $schoolYear = $filter['schoolYear'] ?? NULL;
        $month      = $filter['month'] ?? NULL;
        $editorType = $filter['editorType'] ?? 'all';
        $rubricId   = $filter['rubricId'] ?? NULL;

        return User::withCount(['postsEditInteractions as posts_count' => function ($q) use ($schoolYear, $month, $editorType, $rubricId) {

            $q->select(DB::raw('COUNT(DISTINCT(target_id))'));

            // Filtre par année scolaire et mois
            if ($schoolYear) {
                $start = Carbon::create($schoolYear, 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();
                $q->whereBetween('occurred_at', [$start, $end]);

                // Filtre par mois uniquement si année scolaire précisée
                if ($month) {
                    $q->whereMonth('occurred_at', $month);
                }
            }

            // Filtre par type d'éditeur
            if ($editorType === 'authors') {
                $q->ofType('create');
            } elseif ($editorType === 'correctors') {
                $q->ofType('update');
            }

            // Filtre par rubrique
            if ($rubricId) {
                $q->whereHasMorph(
                    'target',
                    [Post::class],
                    function ($q2) use ($rubricId) {
                        $q2->where('rubric_id', $rubricId);
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
     * Récupère les commentateurs actifs
     *
     * @param array $filter
     *      - commentatorType: 'all' | 'staff' | 'learners'
     *      - schoolYear: int|null
     *      - month: int|null
     */
    public static function getActiveCommentators(array $filter = []) {
        $schoolYear      = $filter['schoolYear'] ?? NULL;
        $month           = $filter['month'] ?? NULL;
        $commentatorType = $filter['commentatorType'] ?? 'all';

        return User::withCount(['postsCommentInteractions as comments_count' => function ($q) use ($schoolYear, $month, $commentatorType) {

            // Filtre par année scolaire
            if ($schoolYear) {
                $start = Carbon::create($schoolYear, 9, 1)->startOfDay();
                $end   = $start->copy()->addYear()->subSecond()->endOfDay();
                $q->whereBetween('occurred_at', [$start, $end]);

                // Filtre par mois uniquement si année scolaire précisée
                if ($month) {
                    $q->whereMonth('occurred_at', $month);
                }
            }

            // Filtre par type d'utilisateur
            if ($commentatorType) {
                $q->byUserType($commentatorType);
            }
        }])
        ->having('comments_count', '>', 0)
        ->orderByDesc('comments_count')
        ->orderBy('name')
        ->orderBy('first_name');
    }

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
