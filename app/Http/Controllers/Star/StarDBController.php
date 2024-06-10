<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use App\User;
use App\StarStudient;
use Illuminate\Support\Facades\Log;

class StarDBController extends Controller
{
    public static function BlendDB()
    {
        try {
            StarStudient::create([
                'code_apprenant' => 21,
                'birthday' => date(12/05/2005),
                'gender' => 'M',
                'level_id' => null,
                'trainning_id' => null,
                'adress' => 'Adresse 5',
                'city' => 'ville',
                'postal_code' => 33562,
                'language' => 'FR',
                'quality' => 'E',
                'status' => 'Apprenti',
                'attendance' => null,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'insertion des étudiants', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Valide les données de l'étudiant.
     *
     * @param array $studient
     * @return bool
     */
    private static function validateStudientData(array $studient)
    {
        return isset($studient['dateNaissance']) && isset($studient['sexe']) && isset($studient['adresse']['adr1']) && isset($studient['adresse']['ville']) && isset($studient['adresse']['cp']) && isset($studient['inscription']['statut']['nomStatut']);
    }
}
