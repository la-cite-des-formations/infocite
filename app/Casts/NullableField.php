<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast Eloquent pour transformer les chaînes vides en NULL lors de la sauvegarde.
 * Utile pour les colonnes de base de données avec contrainte d'unicité ou clé étrangère optionnelle.
 */
class NullableField implements CastsAttributes
{
    /**
     * Transforme la valeur récupérée de la base de données.
     * Aucun changement appliqué en lecture.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key Nom de la colonne.
     * @param  mixed  $value Valeur brute.
     * @param  array  $attributes Attributs complets du modèle.
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    /**
     * Prépare la valeur pour le stockage en base de données.
     * Convertit les valeurs "falsy" (chaîne vide) en NULL.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key Nom de la colonne.
     * @param  mixed  $value Valeur à transformer.
     * @param  array  $attributes Attributs complets du modèle.
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value ?: NULL;
    }
}
