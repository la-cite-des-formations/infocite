<?php

namespace App\Statistics;

use App\Models\Interaction;

/**
 * Statistiques liées aux connexions des utilisateurs.
 * Agrège les interactions de type 'connection' pour le monitoring de l'usage.
 */
class Connections
{
    /**
     * Regroupe les connexions par jour de manière décroissante.
     *
     * @param array $filter Filtres optionnels (ex: userType).
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

    /**
     * Récupère les données de connexion pour les deux dernières semaines (comparatif).
     *
     * @param array $filter Filtres optionnels.
     * @return array Contient les clés 'current' et 'previous' avec les résultats indexés par jour.
     */
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

    /**
     * Regroupe les connexions par mois.
     *
     * @param array $filter Filtres optionnels.
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

    /**
     * Regroupe les connexions par année scolaire et mois pour comparaison N vs N-1.
     *
     * @param array $filter Filtres optionnels.
     * @return array Contient 'previous' et 'current' avec les totaux par mois.
     */
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
