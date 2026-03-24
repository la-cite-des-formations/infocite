<?php

namespace App\Models\Traits;

use App\Models\User;

/**
 * Trait pour la gestion des favoris polymorphiques.
 * Utilisable par Rubric, Post, App.
 */
trait HasFavorites
{
    /**
     * Relation polymorphique vers les utilisateurs ayant mis ce modèle en favori.
     */
    public function favoritedBy()
    {
        return $this->morphToMany(User::class, 'favoriteable', 'favorites')
                    ->withPivot('rank');
    }

    /**
     * Accesseur : Vérifie si le modèle est en favori pour l'utilisateur authentifié.
     * Permet d'utiliser $model->isFavorite dans Blade.
     *
     * @return bool
     */
    public function getIsFavoriteAttribute()
    {
        return $this->favoritedBy()->where('user_id', auth()->id())->exists();
    }

    /**
     * Alterne l'état de favori pour l'utilisateur authentifié.
     *
     * @return bool Nouvel état (true = en favori, false = retiré).
     */
    public function toggleFavorite()
    {
        $userId = auth()->id();
        $relation = $this->favoritedBy();

        if ($relation->where('user_id', $userId)->exists()) {
            $relation->detach($userId);
            return false;
        }

        $relation->attach($userId);
        return true;
    }
}
