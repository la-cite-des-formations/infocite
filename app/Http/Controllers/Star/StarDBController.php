<?php

namespace App\Http\Controllers\Star;

use App\Http\Controllers\Controller;
use App\StarDegree;
use App\StarLevel;
use App\StarSector;
use App\User;
use App\StarStudient;
use App\StarTraining;
use DateTime;
use Exception;

class StarDBController extends Controller
{
    // Fonction principale pour synchroniser les données des étudiants
    public static function BlendDBStudients()
    {
        try {
            // USERS -> APPRENTIS
            $users = User::where('status', 'Apprenti')->get();

            // PARCOURS ALL USERS
            foreach ($users as $user) {
                // GET ALL STUDIENTS // YPAREO //
                $studient = StarYpareoController::getStudient($user['code_ypareo']);
                dd($studient);

                // DATA STUDEINT VALIDE ?
                if (!self::validateStudientData($studient)) {
                    echo "Les données de l'utilisateur sont invalides: " . $user->code_ypareo . PHP_EOL;
                    continue; // Passer à l'utilisateur suivant si les données sont invalides
                }


                // INSCRIPTION VALIDE ?
                if (!isset($studient['inscriptions']) || !is_array($studient['inscriptions']) || empty($studient['inscriptions'])) {
                    echo "Les donnée d'inscription sont invalides: " . $user->code_ypareo . PHP_EOL;
                    continue; // Passer à l'utilisateur suivant si les inscriptions sont invalides
                }
                // LAST INSCRIPTION
                $lastInscription = end($studient['inscriptions']);


                // CLÉ ÉTRANGÈRE //
                // TRAINING
                $trainingID = StarTraining::where('code_training', $lastInscription['formation']['codeFormation'])->get('id');
                // dd($trainingID);

                // LEVEL
                $levelID = StarLevel::where('code_level', $lastInscription['annee']['codeAnnee'])->get('id');
                // dd($levelID);

                // DATE NAISSANCE
                $birthday = DateTime::createFromFormat('d/m/Y', $studient['dateNaissance']);


                // EXISTANT
                $existingStudient = StarStudient::where('code_apprenant', $studient['codeApprenant'])->first();


                // ADRESSE VERIF
                $adresse = self::getValidAddress($studient);


                // QUALITY
                if ($user->quality) {
                    $quality = $user->quality;
                } else {
                    $quality = null;
                }


                // DATA PREPARATION
                $data = [
                    'code_apprenant' => $studient['codeApprenant'],
                    'birthday' => $birthday,
                    'gender' => $studient['sexe'],
                    'level_id' => $levelID,
                    'training_id' => $trainingID,
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


                // EXISTANT
                if ($existingStudient) {
                    // MAJ
                    $existingStudient->update($data);
                } else {
                    // CREATE
                    $newstudient = new StarStudient($data);
                    $newstudient->save();
                }
            }

            // Afficher un message de succès
            echo 'C\'est ok' . PHP_EOL;
        } catch (Exception $e) {
            // Gérer les exceptions et afficher un message d'erreur
            echo 'Erreur lors de l\'insertion des étudiants: ' . $e->getMessage() . PHP_EOL;
        }
    }

    // VALIDE STUDIENT
    private static function validateStudientData(array $studient)
    {
        return isset($studient['dateNaissance'], $studient['sexe'], $studient['inscriptions']) &&
            is_array($studient['inscriptions']) && !empty($studient['inscriptions']);
    }

    // FIND ADRESSE VALIDE
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

    // VALIDE ADRESSE
    private static function validateAddress(array $address)
    {
        return isset($address['adr1'], $address['ville'], $address['cp']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Fonction pour synchroniser les diplômes
    public static function BlendDBDegrees()
    {
        try {
            // Récupérer les formations depuis l'API externe
            $trainings = StarYpareoController::getTrainings();

            // Parcourir chaque formation pour récupérer les diplômes
            foreach (array_slice($trainings, 1) as $training) {
                if ($training['plusUtilise'] == 0) {
                    // Récupérer les données de diplôme
                    $degreeName = $training['diplome']['nomDiplome'];
                    $degreeCode = $training['diplome']['codeDiplome'];

                    // Vérifier si les données de diplôme ne sont pas nulles
                    if ($degreeName && $degreeCode) {
                        // Vérifier si le diplôme existe déjà pour éviter les doublons
                        $existingDegree = StarDegree::where('code_degree', $degreeCode)->first();

                        $data = [
                            'code_degree' => $degreeCode,
                            'name' => $degreeName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        // Verifier si l'apprennant existe
                        if ($existingDegree) {
                            // Mettre à jour l'enregistrement existant
                            $existingDegree->update($data);
                        } else {
                            // Créer un nouvel enregistrement pour l'étudiant
                            self::createNewDegree($data);
                        }
                    } else {
                        // Afficher un message d'avertissement si les données de diplôme sont manquantes
                        echo 'Erreur de Donnée: ' . PHP_EOL;
                        dump($training);
                    }
                }
            }
            // Afficher un message de succès
            echo 'Les Diplomes ont correctement été ajoutés.' . PHP_EOL;
        } catch (Exception $e) {
            // Gérer les exceptions et afficher un message d'erreur
            echo 'Erreur lors de l\'insertion des diplômes: ' . $e->getMessage() . PHP_EOL;
        }
    }
    // Crée un nouvel enregistrement pour l'étudiant
    private static function createNewDegree($data)
    {
        $newDegree = new StarDegree($data);
        $newDegree->save(); // Sauvegarder les données dans la base de données
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function BlendDBSectors()
    {
        try {
            // Récupérer les secteurs depuis l'API externe
            $sectors = StarYpareoController::getSectors();
            // Parcourir chaque secteur
            foreach ($sectors as $sector) {
                if ($sector['plusUtilise'] == 0) {
                    // dd($sector);
                    $existingSector = StarSector::where('code_sector', $sector['codeSecteurActivite'])->first();

                    $data = [
                        'code_sector' => $sector['codeSecteurActivite'],
                        'name' => $sector['nomSecteurActivite'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if (!$existingSector) {
                        // Créer un nouvel enregistrement pour le diplôme
                        $newSector = new StarSector($data);
                        // Sauvegarder le nouveau diplôme dans la base de données
                        $newSector->save();
                    } else {
                        $existingSector->update($data);
                    }
                }
            }
            // Afficher un message de succès
            echo 'Les Diplomes ont correctement été ajoutés.' . PHP_EOL;
        } catch (Exception $e) {
            // Gérer les exceptions et afficher un message d'erreur
            echo 'Erreur lors de l\'insertion des Secteur: ' . $e->getMessage() . PHP_EOL;
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function BlendDBTrainings()
    {
        try {
            // Récupérer les formations depuis l'API externe
            $trainings = StarYpareoController::getTrainings();

            // Parcourir chaque formation
            foreach (array_slice($trainings, 1) as $training) {
                // Tri parmi les anciennes formation plus utilisée
                if ($training['plusUtilise'] == 0) {
                    // dd($training);
                    // Trouver le diplome associé
                    $degreeID = StarDegree::where('code_degree', $training['diplome']['codeDiplome'])->value('id');

                    // dd($degreeID);

                    if (!$degreeID) {
                        echo 'Diplôme non trouvé pour le code: ' . $training['diplome']['codeDiplome'] . PHP_EOL;
                        continue; // Passer à la prochaine formation si le diplôme n'est pas trouvé
                    }

                    $sectorID = StarSector::where('name', $training['nomSecteurActivite'])->value('id');

                    $existingTraining = StarTraining::where('code_training', $training['codeFormation'])->first();

                    if (!$existingTraining) {
                        $newtraining = new StarTraining([
                            'code_training' => $training['codeFormation'],
                            'name_training' => $training['nomFormation'],
                            'sector_id' => $sectorID,
                            'degree_id' => $degreeID,

                        ]);
                        $newtraining->save();
                    }
                }
            }
            // Afficher un message de succès
            echo 'Les Formations ont correctement été ajoutés.' . PHP_EOL;
        } catch (Exception $e) {
            // Gérer les exceptions et afficher un message d'erreur
            echo 'Erreur lors de l\'insertion des Formations: ' . $e->getMessage() . PHP_EOL;
        }
    }
}
