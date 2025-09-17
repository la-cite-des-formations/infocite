<?php

namespace App\Statistics;

use App\Models\Interaction;

class Connections
{
    public static function groupByDay($filter = []) {
        return Interaction::ofType('connection')
            ->byUserType($filter['userType'] ?? null)
            ->selectRaw('interaction_at, count(*) as `connections_nb`')
            ->groupBy('interaction_at')
            ->orderBy('interaction_at', 'desc');
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
            ->selectRaw('DATE(interaction_at) as day, COUNT(*) as connections_nb')
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
                DATE_FORMAT(interaction_at, "%Y-%m") as `year_month`,
                count(DISTINCT interactions.user_id) as `connections_nb`
            ')
            ->groupBy('year_month')
            ->orderBy('year_month', 'desc');
    }

    public static function groupBySchoolYearAndMonth(array $filter = []) {
        $currentStartYear  = now()->month >= 9 ? now()->year : now()->year - 1;
        $previousStartYear = $currentStartYear - 1;

        $query = function ($startYear) use ($filter) {
            return Interaction::ofType('connection')
                ->byUserType($filter['userType'] ?? null)
                ->forSchoolYear($startYear)
                ->selectRaw('
                    DATE_FORMAT(interaction_at, "%Y-%m") as `year_month`,
                    COUNT(DISTINCT interactions.user_id) as `connections_nb`
                ')
                ->groupBy('year_month')
                ->pluck('connections_nb', 'year_month');
        };

        return [
            'previous' => $query($previousStartYear),
            'current'  => $query($currentStartYear),
            'labels'   => [
                'previous' => "Année précédente ({$previousStartYear}-" . ($previousStartYear + 1) . ")",
                'current'  => "Année en cours ({$currentStartYear}-" . ($currentStartYear + 1) . ")",
            ]
        ];
    }
}
