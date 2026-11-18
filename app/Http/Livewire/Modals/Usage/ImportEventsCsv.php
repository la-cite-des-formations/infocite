<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Post;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Modale Livewire pour l'import d'événements en masse depuis un fichier CSV Google Sheets.
 */
class ImportEventsCsv extends Component
{
    use WithFileUploads;

    public $csvFile;
    public bool $importFinished = false;
    public int $importedCount = 0;
    public int $processedTotal = 0;
    public array $errorsList = [];

    protected $rules = [
        'csvFile' => 'required|file|max:5120',
    ];

    /**
     * Télécharge un modèle de fichier CSV prêt à l'emploi pour Google Sheets.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modele_import_evenements.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Écrire le BOM UTF-8 pour Excel / Google Sheets
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'titre',
                'description',
                'type_evenement',
                'date_debut',
                'heure_debut',
                'date_fin',
                'heure_fin',
                'lieu',
                'id_article'
            ]);

            $sampleType = EventType::first()?->name ?? 'Séminaire';

            fputcsv($handle, [
                'Séminaire pédagogique de rentrée',
                'Présentation des objectifs et calendrier des projets de l\'année.',
                $sampleType,
                today()->format('Y-m-d'),
                '09:00',
                today()->format('Y-m-d'),
                '17:00',
                'Amphithéâtre',
                ''
            ]);

            fputcsv($handle, [
                'Portes ouvertes CFA',
                'Accueil des candidats et visites des ateliers de formation.',
                'Portes ouvertes',
                today()->addDays(14)->format('Y-m-d'),
                '10:00',
                today()->addDays(14)->format('Y-m-d'),
                '16:30',
                'Campus principal',
                ''
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Traite l'importation du fichier CSV.
     */
    public function import()
    {
        $this->validate();

        $path = $this->csvFile->getRealPath();
        $this->errorsList = [];
        $this->importedCount = 0;
        $this->processedTotal = 0;

        if (!($handle = fopen($path, 'r'))) {
            $this->addError('csvFile', 'Impossible d\'ouvrir le fichier téléversé.');
            return;
        }

        // Détection et suppression du BOM UTF-8 éventuel
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Lecture de la ligne d'en-tête
        $header = fgetcsv($handle, 0, ',');
        if (!$header) {
            $this->addError('csvFile', 'Le fichier CSV est vide.');
            fclose($handle);
            return;
        }

        // Normalisation des en-têtes (en minuscules, sans accents)
        $normalizedHeader = array_map(function ($col) {
            $col = trim($col);
            $col = mb_strtolower($col, 'UTF-8');
            $col = str_replace(['é', 'è', 'ê', 'ë'], 'e', $col);
            $col = str_replace(['à', 'â'], 'a', $col);
            return $col;
        }, $header);

        // Recherche des index des colonnes
        $colMap = [
            'title' => $this->findColumnIndex($normalizedHeader, ['titre', 'title', 'nom']),
            'description' => $this->findColumnIndex($normalizedHeader, ['description', 'desc', 'contenu']),
            'type' => $this->findColumnIndex($normalizedHeader, ['type_evenement', 'type', 'event_type', 'event_type_id', 'categorie']),
            'start_date' => $this->findColumnIndex($normalizedHeader, ['date_debut', 'start_date', 'debut_date', 'date']),
            'start_time' => $this->findColumnIndex($normalizedHeader, ['heure_debut', 'start_time', 'debut_heure', 'heure']),
            'end_date' => $this->findColumnIndex($normalizedHeader, ['date_fin', 'end_date', 'fin_date']),
            'end_time' => $this->findColumnIndex($normalizedHeader, ['heure_fin', 'end_time', 'fin_heure']),
            'location' => $this->findColumnIndex($normalizedHeader, ['lieu', 'location', 'salle', 'endroit']),
            'post_id' => $this->findColumnIndex($normalizedHeader, ['id_article', 'post_id', 'article_id', 'article']),
        ];

        if ($colMap['title'] === null || $colMap['description'] === null || $colMap['type'] === null || $colMap['start_date'] === null) {
            $this->addError('csvFile', 'Colonnes obligatoires manquantes dans l\'en-tête (titre, description, type, date_debut).');
            fclose($handle);
            return;
        }

        $allTypes = EventType::all();
        $lineNumber = 1;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $lineNumber++;

            // Ignorer les lignes totalement vides
            if (empty(array_filter($row))) {
                continue;
            }

            $this->processedTotal++;

            $title = trim($row[$colMap['title']] ?? '');
            $desc = trim($row[$colMap['description']] ?? '');
            $typeRaw = trim($row[$colMap['type']] ?? '');
            $startDateRaw = trim($row[$colMap['start_date']] ?? '');

            // Résolution de l'article lié en amont pour permettre la substitution
            $postId = null;
            $linkedPost = null;
            if ($colMap['post_id'] !== null && !empty($row[$colMap['post_id']])) {
                $pid = (int) trim($row[$colMap['post_id']]);
                $linkedPost = Post::find($pid);
                if ($linkedPost) {
                    $postId = $pid;
                }
            }

            // Éléments de substitution si id_article renseigné et titre/description manquant(s)
            if ($linkedPost) {
                if (empty($title)) {
                    $title = $linkedPost->title;
                }
                if (empty($desc)) {
                    $cleanContent = strip_tags($linkedPost->content);
                    $desc = !empty(trim($cleanContent)) ? mb_substr(trim($cleanContent), 0, 500) : $linkedPost->title;
                }
            }

            if (empty($title)) {
                $this->errorsList[] = "Ligne $lineNumber : Le titre de l'événement est obligatoire.";
                continue;
            }

            if (empty($desc)) {
                $this->errorsList[] = "Ligne $lineNumber : La description est obligatoire pour \"$title\".";
                continue;
            }

            if (empty($startDateRaw)) {
                $this->errorsList[] = "Ligne $lineNumber : La date de début est obligatoire pour \"$title\".";
                continue;
            }

            // Résolution du type d'événement (par ID ou par Nom)
            $eventType = null;
            if (is_numeric($typeRaw)) {
                $eventType = $allTypes->firstWhere('id', (int) $typeRaw);
            }
            if (!$eventType) {
                $lowerType = mb_strtolower($typeRaw, 'UTF-8');
                $eventType = $allTypes->first(function ($t) use ($lowerType) {
                    return mb_strtolower($t->name, 'UTF-8') === $lowerType;
                });
            }

            if (!$eventType) {
                $this->errorsList[] = "Ligne $lineNumber : Type d'événement \"$typeRaw\" introuvable pour \"$title\".";
                continue;
            }

            // Parsing de la date de début
            try {
                $startDate = $this->parseDate($startDateRaw);
            } catch (\Exception $e) {
                $this->errorsList[] = "Ligne $lineNumber : Format de date de début \"$startDateRaw\" invalide pour \"$title\".";
                continue;
            }

            // Heure de début
            $startTime = null;
            if ($colMap['start_time'] !== null && !empty($row[$colMap['start_time']])) {
                $startTime = $this->cleanTime($row[$colMap['start_time']]);
            }

            // Date de fin
            $endDate = null;
            if ($colMap['end_date'] !== null && !empty($row[$colMap['end_date']])) {
                try {
                    $endDate = $this->parseDate($row[$colMap['end_date']]);
                } catch (\Exception $e) {
                    $this->errorsList[] = "Ligne $lineNumber : Format de date de fin invalide pour \"$title\".";
                }
            }

            // Heure de fin
            $endTime = null;
            if ($colMap['end_time'] !== null && !empty($row[$colMap['end_time']])) {
                $endTime = $this->cleanTime($row[$colMap['end_time']]);
            }

            // Lieu
            $location = null;
            if ($colMap['location'] !== null && !empty($row[$colMap['location']])) {
                $location = mb_substr(trim($row[$colMap['location']]), 0, 150);
            }

            // Création de l'événement
            Event::create([
                'title' => $title,
                'description' => $desc,
                'event_type_id' => $eventType->id,
                'start_date' => $startDate,
                'start_time' => $startTime,
                'end_date' => $endDate,
                'end_time' => $endTime,
                'location' => $location,
                'post_id' => $postId,
            ]);

            $this->importedCount++;
        }

        fclose($handle);
        $this->importFinished = true;

        $this->emit('eventImported');
        $this->emit('refreshAgenda');
    }

    private function findColumnIndex(array $headers, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $idx = array_search($candidate, $headers);
            if ($idx !== false) {
                return $idx;
            }
        }
        return null;
    }

    private function parseDate(string $raw): string
    {
        $raw = trim($raw);
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $raw)) {
            return Carbon::createFromFormat('d/m/Y', $raw)->format('Y-m-d');
        }
        return Carbon::parse($raw)->format('Y-m-d');
    }

    private function cleanTime(string $raw): ?string
    {
        $raw = trim($raw);
        if (empty($raw)) return null;

        if (preg_match('/^(\d{1,2})[h:](\d{2})/', $raw, $matches)) {
            return sprintf('%02d:%02d:00', (int)$matches[1], (int)$matches[2]);
        }

        try {
            return Carbon::parse($raw)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.modals.usage.import-events-csv');
    }
}
