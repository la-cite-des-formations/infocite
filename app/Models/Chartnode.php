<?php

namespace App\Models;

use App\Casts\NullableField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\WithSearching;

/**
 * Représente un nœud dans l'organigramme structurel.
 *
 * Contrairement au modèle Actor (qui est centré sur les personnes),
 * Chartnode représente les unités organisationnelles ou les fonctions (ex: Direction, Service Informatique).
 */
class Chartnode extends Model
{
    use WithSearching;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'parent_id', 'format_id', 'rank', 'code_fonction'];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parent_id' => NullableField::class,
    ];

    /**
     * Accesseur pour la liste des acteurs associés à ce nœud (identités concaténées).
     *
     * @return string
     */
    public function getActorsListAttribute() {
        return $this->actors->pluck('chartnode_identity')->implode(', ');
    }

    /**
     * Accesseur pour le formatage HTML de la boîte du nœud dans l'organigramme.
     *
     * @return string HTML formaté.
     */
    public function getBoxFormatAttribute() {
        return is_object($this->format) ?

            "<p class='fw-bold {$this->format->title_color}'>{$this->name}</p>".
            "<p class='{$this->format->subtitle_color}'>".$this->actors_list."</p>" :

            "<p class='fw-bold'>{$this->name}</p>".
            "<p>".$this->actors_list."</p>";
    }

    /**
     * Accesseur pour récupérer le groupe associé via le code fonction Ypareo.
     *
     * @return Group|null
     */
    public function getGroupAttribute() {
        return Group::query()
            ->where('type', 'P')
            ->where('code_ypareo', $this->code_fonction)
            ->first();
    }

    /**
     * Relation vers le format visuel appliqué à ce nœud.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function format() {
        return $this
            ->belongsTo('App\Models\Format');
    }

    /**
     * Récupère les acteurs (utilisateurs) associés à ce nœud via son groupe.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function actors() {
        if (is_null($this->group)) {
            return (new Group)->users();
        }

        return $this->group->users();
    }

    /**
     * Relation vers le nœud parent dans la hiérarchie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        return $this
            ->belongsTo('App\Models\Chartnode', 'parent_id');
    }

    /**
     * Relation vers les nœuds enfants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs() {
        return $this
            ->hasMany('App\Models\Chartnode', 'parent_id');
    }

    /**
     * Prépare les données pour l'organigramme structurel.
     * Peut être restreint à un sous-arbre si un $node est fourni.
     *
     * @param Chartnode|null $node Nœud racine éventuel pour restreindre l'affichage.
     * @return array Données formatées pour Google Charts.
     */
    public static function getOrgChart($node = NULL) {
        switch (TRUE) {
            case is_object($node) :
                $chartnodes = new Collection();
                if (is_object($node->parent)) {
                    $node->parent->parent_id = NULL;
                    $chartnodes->add($node->parent);
                }
                else {
                    $node->parent_id = NULL;
                }
                $chartnodes->add($node);
                $chartnodes = $chartnodes->merge($node->childs->sortBy('rank'));
            break;

            default :
                $chartnodes = static::all()
                    ->sortBy('rank');
        }

        $orgChartBoxes = new Collection();

        $chartnodes
            ->each(function ($chartnode) use ($orgChartBoxes) {
                $orgChartBoxes->add([
                    'c' => [
                        [
                            'v' => (string) $chartnode->id,
                            'f' => $chartnode->boxFormat
                        ],
                        ['v' => (string) $chartnode->parent_id],
                        ['v' => ''],
                    ],
                    'p' => ['style' => is_object($chartnode->format) ? $chartnode->format->style : ''],
                ]);
            });

        return [
            'cols' => [
                ['label' => 'NodeId', 'type' => 'string'],
                ['label' => 'NodeParentId', 'type' => 'string'],
                ['label' => 'ToolTip', 'type' => 'string'],
            ],
            'rows' => $orgChartBoxes
        ];
    }

    /**
     * Sauvegarde les données de l'organigramme structurel dans un fichier JSON.
     *
     * @return void
     */
    public static function saveOrgChartData() {
        Storage::put(
            'public/orgchart/chartnodes.json',
            json_encode(self::getOrgChart(), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Filtre les nœuds selon un critère de recherche.
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $chartnodes = static::all()
            ->when($search, function ($chartnodes) use ($search) {
                return $chartnodes->filter(function ($chartnode) use ($search) {
                    $columns = [$chartnode->name];
                    if (is_object($chartnode->parent)) $columns[] = $chartnode->parent->name;
                    if (is_object($chartnode->group)) $columns[] = $chartnode->group->name;

                    return static::tableContains($columns, $search);
                });
            });

        return $chartnodes->isEmpty() ? static::whereNull('id') : $chartnodes->toQuery();
    }
}
