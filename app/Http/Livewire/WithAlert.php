<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Collection;

/**
 * Trait pour la gestion des alertes et messages flash en session dans les composants Livewire.
 */
trait WithAlert
{
    /**
     * Envoie une alerte en session.
     *
     * @param array $alertData Données de l'alerte (alertClass, message, etc.).
     */
    private function sendAlert($alertData) {
        foreach($alertData as $key => $value) {
            session()->flash($key, $value);
        }
    }

    /**
     * Vérifie si un attribut est vide et envoie une alerte si c'est le cas.
     *
     * @param string $attr Nom de l'attribut à vérifier.
     * @param string $message Message d'erreur.
     * @return bool True si vide, false sinon.
     */
    private function isEmpty($attr, $message = "") {
        if (empty($this->$attr)) {
            $this->sendAlert([
                'alertClass' => 'warning',
                'message' => (new Collection([$message, "Recommencer SVP"]))->implode('. '),
            ]);

            return TRUE;
        }
        return FALSE;
    }
}
