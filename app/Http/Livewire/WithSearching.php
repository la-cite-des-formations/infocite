<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;

/**
 * Trait utilitaire pour effectuer des recherches textuelles simplifiées dans des tableaux de chaînes.
 * Utilisé principalement pour le filtrage côté serveur dans les composants Livewire.
 */
trait WithSearching
{
    /**
     * Vérifie si l'un des éléments d'un tableau contient la chaîne recherchée (insensible à la casse/slugifié).
     *
     * @param array $tableElements Tableau de chaînes.
     * @param string $searchedString Chaîne recherchée.
     * @return bool
     */
    public static function tableContains($tableElements, $searchedString) {
        foreach($tableElements as $element) {
            if (Str::of(Str::slug($element))->contains(Str::slug($searchedString))) return TRUE;
        }
        return FALSE;
    }
}
