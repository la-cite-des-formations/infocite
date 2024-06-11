<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use App\User;
use App\StarStudient;
use DateTime;

class StarDBController extends Controller
{
    public static function BlendDB()
    {
        try {
            $users = User::where('status', 'Apprenti')->get();

            foreach ($users as $user) {
                $studient = StarYpareoController::getStudient($user['code_ypareo']);

                // Validation des données de l'étudiant
                if (self::validateStudientData($studient)) {
                    // Vérifiez si 'inscriptions' est un tableau et non vide
                    if (isset($studient['inscriptions']) && is_array($studient['inscriptions']) && !empty($studient['inscriptions'])) {
                        // Récupération de la dernière inscription
                        $lastInscription = end($studient['inscriptions']);

                        $birthday = DateTime::createFromFormat('d/m/Y', $studient['dateNaissance']);

                        // Vérifiez si l'enregistrement existe déjà
                        $existingStudient = StarStudient::where('code_apprenant', $studient['codeApprenant'])->first();

                        // Trouver une adresse valide
                        $adresse = self::getValidAddress($studient);

                        if ($user->quality) {
                            $quality = $user->quality;
                        }
                        else {
                            $quality = null;
                        }

                        $data = [
                            'birthday' => $birthday,
                            'gender' => $studient['sexe'],
                            'level_id' => null,
                            'trainning_id' => null,
                            'adress' => $adresse['adr1'] ?? null,
                            'city' => $adresse['ville'] ?? null,
                            'postal_code' => $adresse['cp'] ?? null,
                            'language' => $user->language ?? null,
                            'quality' => $quality,
                            'status' => $lastInscription['statut']['nomStatut'],
                            'attendance' => null,
                            'updated_at' => now(),
                        ];

                        if ($existingStudient) {
                            // Mettre à jour l'enregistrement existant
                            $existingStudient->update($data);
                        } else {
                            // Créez une nouvelle instance de StarStudient
                            $data['code_apprenant'] = $studient['codeApprenant'];
                            $data['created_at'] = now();

                            $newstudient = new StarStudient($data);

                            // Enregistrez les données dans la base de données
                            $newstudient->save();
                        }
                    } else {
                        echo 'Inscription data is not valid or empty for user: ' . $user->code_ypareo . PHP_EOL;
                    }
                } else {
                    echo 'Invalid data for user: ' . $user->code_ypareo . PHP_EOL;
                }
            }

            echo 'C\'est ok' . PHP_EOL;
        } catch (\Exception $e) {
            echo 'Erreur lors de l\'insertion des étudiants: ' . $e->getMessage() . PHP_EOL;
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
        return isset($studient['dateNaissance']) &&
            isset($studient['sexe']) &&
            isset($studient['inscriptions']) &&
            is_array($studient['inscriptions']) &&
            !empty($studient['inscriptions']);
    }

    /**
     * Trouve une adresse valide parmi les adresses fournies.
     *
     * @param array $studient
     * @return array|null
     */
    private static function getValidAddress(array $studient)
    {
        $addresses = ['adresse', 'adresse2', 'adresse3'];
        foreach ($addresses as $addressKey) {
            if (isset($studient[$addressKey]) && self::validateAddress($studient[$addressKey])) {
                return $studient[$addressKey];
            }
        }
        return null;
    }

    /**
     * Valide les données de l'adresse.
     *
     * @param array $address
     * @return bool
     */
    private static function validateAddress(array $address)
    {
        return isset($address['adr1']) && isset($address['ville']) && isset($address['cp']);
    }
}


///////////////////////////////////////////////////////////