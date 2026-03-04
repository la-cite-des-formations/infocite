<?php

namespace App\Models\Traits;

use App\Models\Interaction;
use Carbon\Carbon;

/**
 * Trait permettant à un modèle de gérer des interactions utilisateur (vues, commentaires, éditions, etc.).
 * Fournit des relations polymorphiques et des méthodes de vérification/enregistrement d'interactions.
 */
trait HasInteractions
{
    /**
     * Relation polymorphique avec les interactions.
     */
    public function interactions() {
        return $this
            ->morphMany(Interaction::class, 'target')
            ->orderByRaw('user_id ASC, occurred_at DESC');
    }

    /**
     * Relation générique filtrée par type d'interaction
     */
    public function interactionsOfType(string $type) {
        return $this->interactions()->ofType($type);
    }

    /**
     * Relations spécifiques pour les types principaux
     */
    public function viewInteractions() {
        return $this->interactionsOfType('view');
    }

    public function commentInteractions() {
        return $this->interactionsOfType('comment');
    }

    public function createInteractions() {
        return $this->interactionsOfType('create');
    }

    public function updateInteractions() {
        return $this->interactionsOfType('update');
    }

    public function deleteInteractions() {
        return $this->interactionsOfType('delete');
    }

    /**
     * Vérifie si l'instance du modèle a fait l'objet d'une interaction avec l'utilisateur courant :
     * - soit dans la journée,
     * - soit à une date précise sans heure précise
     * - soit à une date et heure précises
     *
     * @param string $type Type de l'interaction (view, edit, comment, etc.)
     * @param \Carbon\Carbon|string|null $mixedDate Date de l'interaction avec ou sans heure précise (par défaut aujourd'hui)
     * @return bool
     */
    public function hasInteraction(string $type, $mixedDate = null): bool {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Parser la date et déterminer si on compare uniquement la date
        $datetime = is_null($mixedDate) ? today() : Carbon::parse($mixedDate);
        $compareDateOnly = $datetime->isStartOfDay();

        // Callback pour comparer deux dates selon le type de comparaison
        $matches = fn($interactionDate) => $compareDateOnly
            ? $interactionDate->isSameDay($datetime)
            : $interactionDate->eq($datetime);

        // Vérification en mémoire si la relation est déjà chargée
        if ($this->relationLoaded('interactions')) {
            return $this->interactions
                ->where('type', $type)
                ->where('user_id', $user->id)
                ->contains(fn($interaction) => $matches($interaction->occurred_at));
        }

        // Vérification via requête SQL
        $query = $this->interactions()->byUser($user)->ofType($type);

        if ($compareDateOnly) {
            $query->forDate($datetime);
        }
        else {
            $query->forDatetime($datetime);
        }

        return $query->exists();
    }

    /**
     * Vérifie l'existance d'une interaction en rapport avec l'instance du modèle
     * à une certaine date avec ou sans heure précise (par défaut aujourd'hui)
     * et procède à son enregistrement si nécessaire
    *
    * @param string $type Type de l'interaction (view, edit, comment, etc.)
    * @param \Carbon\Carbon|string|null $mixedDate Date de l'interaction avec ou sans heure précise (par défaut aujourd'hui)
    * @param int|null $userId Id de l'utilisateur (par défaut l'utilisateur courant)
    * @return \App\Models\Interaction
     */
    public function ensureInteraction(string $type, $mixedDate = NULL, ?int $userId = NULL): Interaction {
        $userId = $userId ?? auth()->id();
        $datetime = is_null($mixedDate) ? today() : Carbon::parse($mixedDate);

        return $this->interactions()->firstOrCreate([
            'user_id' => $userId,
            'type' => $type,
            'occurred_at' => $datetime,
        ]);
    }
}
