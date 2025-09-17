<?php

namespace App\Models\Traits;

use App\Models\Interaction;

trait HasInteractions
{
    /**
     * Relation polymorphique avec les interactions.
     */
    public function interactions()
    {
        return $this
            ->morphMany(Interaction::class, 'target')
            ->orderByRaw('user_id ASC, interaction_at DESC');
    }

    /**
     * Vérifie si l'utilisateur courant a effectué une interaction donnée à une date précise.
     *
     * @param string $type  Type de l'interaction (view, edit, comment, etc.)
     * @param \Carbon\Carbon|string|null $date  Date de l'interaction (par défaut aujourd'hui)
     * @return bool
     */
    public function hasInteraction(string $type, $date = NULL): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $date = $date ?? today();

        // Si la relation interactions est déjà chargée, on filtre en mémoire
        if ($this->relationLoaded('interactions')) {
            return $this->interactions
                ->where('type', $type)
                ->where('user_id', $user->id)
                ->contains(fn ($interaction) => $interaction->interaction_at->isSameDay($date));
        }

        // Sinon, on utilise le scope forDate() pour la requête SQL
        return $this->interactions()
            ->byUser($user)
            ->ofType($type)
            ->forDate($date)
            ->exists();
    }

    /**
     * Vérifie l'existance d'une interaction à une certaine date
     * et procède à son enregistrement si nécessaire
     */
    public function ensureInteraction(string $type, $date = NULL): void {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if (! $this->hasInteraction($type, $date)) {
            $this->interactions()->create([
                'user_id' => $user->id,
                'type' => $type,
                'interaction_at' => $date ?? today(),
            ]);
        }
    }
}
