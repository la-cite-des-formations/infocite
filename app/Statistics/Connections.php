<?php

namespace App\Statistics;

use App\Models\Interaction;

class Connections
{
    public static function groupByDay($filter = []) {
        return Interaction::ofType('connection')
            ->byUserType($filter['userType'] ?? null)
            ->selectRaw('
                occurred_at,
                count(DISTINCT interactions.user_id) as `connections_nb`
            ')
            ->groupBy('occurred_at')
            ->orderBy('occurred_at', 'desc');
    }

    public static function groupByLastTwoWeeks(array $filter = []) {
        $today = today();

        // Début de la semaine actuelle (lundi)
        $currentWeekStart = $today->copy()->startOfWeek();
        $currentWeekEnd   = $today->copy()->endOfWeek();

        // Début et fin de la semaine précédente
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd   = $currentWeekEnd->copy()->subWeek();

        $query = fn($start, $end) => Interaction::ofType('connection')
            ->byUserType($filter['userType'] ?? null)
            ->selectRaw('
                DATE(occurred_at) as day,
                COUNT(DISTINCT interactions.user_id) as connections_nb
            ')
            ->betweenDates($start, $end)
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->get()
            ->keyBy('day');

        return [
            'current'  => $query($currentWeekStart, $currentWeekEnd),
            'previous' => $query($previousWeekStart, $previousWeekEnd),
        ];
    }

    public static function groupByMonth($filter = []) {
        return Interaction::ofType('connection')
            ->byUserType($filter['userType'] ?? null)
            ->selectRaw('
                DATE_FORMAT(occurred_at, "%Y-%m") as `year_month`,
                COUNT(DISTINCT interactions.user_id) as `connections_nb`
            ')
            ->groupBy('year_month')
            ->orderBy('year_month', 'desc');
    }

    public static function groupBySchoolYearAndMonth(array $filter = []) {
        $today = now();
        $startSchoolYear  = $today->month >= 9 ? $today->year : $today->year - 1;

        $query = function ($startYear) use ($filter) {
            return Interaction::ofType('connection')
                ->byUserType($filter['userType'] ?? null)
                ->forSchoolYear($startYear)
                ->selectRaw('
                    DATE_FORMAT(occurred_at, "%Y-%m") as `year_month`,
                    COUNT(DISTINCT interactions.user_id) as `connections_nb`
                ')
                ->groupBy('year_month')
                ->pluck('connections_nb', 'year_month');
        };

        return [
            'previous' => $query($startSchoolYear - 1),
            'current'  => $query($startSchoolYear),
        ];
    }
}
