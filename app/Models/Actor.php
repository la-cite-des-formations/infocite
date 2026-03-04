<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Représente un acteur de l'organigramme (Manager ou subordonné).
 *
 * Cette classe gère la structure hiérarchique de l'organisation :
 * - Liaison avec le modèle User.
 * - Gestion des managers et de leurs subordonnés.
 * - Génération des données pour l'affichage de l'organigramme (format JSON).
 */
class Actor extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['id', 'manager_id'];

    /**
     * Indique si les IDs sont auto-incrémentés.
     * Ici false car l'ID est lié à celui de l'utilisateur.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indique si le modèle doit avoir des timestamps (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * Relation vers l'utilisateur correspondant à cet acteur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this
            ->belongsTo('App\Models\User', 'id');
    }

    /**
     * Accesseur pour l'identité de l'acteur (via User).
     *
     * @return string
     */
    public function getIdentityAttribute() {
        return $this->user->identity;
    }

    /**
     * Accesseur pour la liste des fonctions de l'utilisateur (type 'P').
     *
     * @return string
     */
    public function getFunctionsListAttribute() {
        return $this->user->functionsList(['P']);
    }

    /**
     * Accesseur pour le formatage HTML d'une boîte de manager dans l'organigramme.
     *
     * @return string HTML formaté.
     */
    public function getManagerBoxFormatAttribute() {
        $format = Format::find($this->format_id);

        return
            "<p class='fw-bold {$format->title_color}'>{$this->identity}</p>".
            "<p class='{$format->subtitle_color}'>{$this->functionsList}</p>";
    }

    /**
     * Relation vers le manager direct de cet acteur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function manager() {
        return $this
            ->hasOne('App/Actor', 'manager_id');
    }

    /**
     * Récupère les processus métiers associés à l'utilisateur.
     *
     * @return \Illuminate\Support\Collection
     */
    public function processes() {
        return $this->user->processes();
    }

    /**
     * Accesseur pour le formatage HTML de la liste des subordonnés directs.
     * Exclut les subordonnés qui sont eux-mêmes managers.
     *
     * @return string HTML formaté.
     */
    public function getFullSubordinatesListBoxFormatAttribute() {
        $formatedFullSubordinates = new Collection();

        $this->user->subordinates->filter(function ($subordinate) {
            return !$subordinate->isManager();
        })->each(function ($subordinate) use ($formatedFullSubordinates) {
            $formatedFullSubordinates->add(
                '<p>'.
                    '<div class="text-danger">'.$subordinate->identity.'</div>'.
                    $subordinate->functionsList(['P']).
                '</p>'
            );
        });

        return $formatedFullSubordinates->implode('');
    }

    /**
     * Accesseur vérifiant si l'acteur est un manager.
     *
     * @return bool
     */
    public function getIsManagerAttribute() {
        return $this->user->isManager();
    }

    /**
     * Récupère la liste de tous les managers définis dans les processus.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getManagers() {
        return static::query()
            ->distinct()
            ->join('processes', 'actors.id', '=', 'processes.manager_id')
            ->whereNotNull('processes.manager_id')
            ->orderBy('rank')
            ->get(['actors.*', 'processes.format_id']);
    }

    /**
     * Prépare les données pour l'affichage de l'organigramme (Google Charts format).
     * Inclut les boîtes de managers et les listes de subordonnés.
     *
     * @return \Illuminate\Support\Collection Données formatées pour l'organigramme.
     */
    public static function getOrgChart() {
        $orgChartBoxes = new Collection();

        self::getManagers()->each(function ($manager) use ($orgChartBoxes) {
            $orgChartBoxes->add([
                'data' => [
                    [
                        'v' => (string) $manager->id,
                        'f' => $manager->managerBoxFormat
                    ],
                    (string) $manager->manager_id,
                    '',
                ],
                'style' => Format::find($manager->format_id)->style
            ]);

            $orgChartBoxes->add([
                'data' => [
                    $manager->fullSubordinatesListBoxFormat,
                    (string) $manager->id,
                    '',
                ],
                'style' => Format::firstWhere('name', 'Default')->style,
            ]);
        });

        return $orgChartBoxes;
    }

    /**
     * Sauvegarde les données de l'organigramme dans un fichier JSON public.
     *
     * @return void
     */
    public static function saveOrgChartData() {
        Storage::put(
            'public/orgchart/actors.json',
            json_encode(self::getOrgChart()->pluck('data'), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
        );
    }
}
