<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use App\User;
use App\StarStudient;
use DateTime;
use Exception;

class StarDBController extends Controller
{
    // Fonction principale pour synchroniser les données des étudiants
    public static function BlendDB()
    {
        try {
            // Récupération des utilisateurs ayant le statut 'Apprenti'
            $users = User::where('status', 'Apprenti')->get();

            // Parcourir chaque utilisateur pour synchroniser leurs données
            foreach ($users as $user) {
                // Récupérer les données de l'étudiant depuis une API externe
                $studient = StarYpareoController::getStudient($user['code_ypareo']);

                // Valider les données de l'étudiant
                if (!self::validateStudientData($studient)) {
                    echo 'Invalid data for user: ' . $user->code_ypareo . PHP_EOL;
                    continue; // Passer à l'utilisateur suivant si les données sont invalides
                }

                // Vérifier si les inscriptions sont valides
                if (!isset($studient['inscriptions']) || !is_array($studient['inscriptions']) || empty($studient['inscriptions'])) {
                    echo 'Inscription data is not valid or empty for user: ' . $user->code_ypareo . PHP_EOL;
                    continue; // Passer à l'utilisateur suivant si les inscriptions sont invalides
                }

                // Récupérer la dernière inscription
                $lastInscription = end($studient['inscriptions']);
                
                // Convertir la date de naissance en objet DateTime
                $birthday = DateTime::createFromFormat('d/m/Y', $studient['dateNaissance']);
                
                // Vérifier si l'étudiant existe déjà dans la base de données
                $existingStudient = StarStudient::where('code_apprenant', $studient['codeApprenant'])->first();
                
                // Trouver une adresse valide parmi les adresses fournies
                $adresse = self::getValidAddress($studient);

                if ($user->quality) {
                    $quality = $user->quality;
                }else {
                    $quality = null;
                }

                // Préparer les données de l'étudiant pour l'insertion/mise à jour
                $data = self::prepareStudientData($user, $studient, $lastInscription, $birthday, $adresse, $quality);

                if ($existingStudient) {
                    // Mettre à jour l'enregistrement existant
                    $existingStudient->update($data);
                } else {
                    // Créer un nouvel enregistrement pour l'étudiant
                    self::createNewStudient($data);
                }
            }

            // Afficher un message de succès
            echo 'C\'est ok' . PHP_EOL;
        } catch (Exception $e) {
            // Gérer les exceptions et afficher un message d'erreur
            echo 'Erreur lors de l\'insertion des étudiants: ' . $e->getMessage() . PHP_EOL;
        }
    }

    // Valide les données de l'étudiant
    private static function validateStudientData(array $studient)
    {
        return isset($studient['dateNaissance'], $studient['sexe'], $studient['inscriptions']) &&
               is_array($studient['inscriptions']) && !empty($studient['inscriptions']);
    }

    // Trouve une adresse valide parmi les adresses fournies
    private static function getValidAddress(array $studient)
    {
        $addresses = ['adresse', 'adresse2', 'adresse3'];
        foreach ($addresses as $addressKey) {
            if (isset($studient[$addressKey]) && self::validateAddress($studient[$addressKey])) {
                return $studient[$addressKey]; // Retourner la première adresse valide trouvée
            }
        }
        return null; // Retourner null si aucune adresse valide n'est trouvée
    }

    // Valide les données de l'adresse
    private static function validateAddress(array $address)
    {
        return isset($address['adr1'], $address['ville'], $address['cp']);
    }

    // Préparer les données de l'étudiant pour l'insertion/mise à jour
    private static function prepareStudientData($user, $studient, $lastInscription, $birthday, $adresse, $quality)
    {
        return [
            'code_apprenant' => $studient['codeApprenant'],
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
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Crée un nouvel enregistrement pour l'étudiant
    private static function createNewStudient($data)
    {
        $newstudient = new StarStudient($data);
        $newstudient->save(); // Sauvegarder les données dans la base de données
    }
}
