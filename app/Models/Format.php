<?php

namespace App\Models;

use App\Casts\NullableField;
use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente un format visuel applicable aux nœuds de l'organigramme ou aux acteurs.
 * Définit les couleurs de fond, de bordure et de texte.
 */
class Format extends Model
{
    use WithSearching;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'bg_color', 'border_style', 'title_color', 'subtitle_color'];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bg_color' => NullableField::class,
        'border_style' => NullableField::class,
        'title_color' => NullableField::class,
        'subtitle_color' => NullableField::class,
    ];

    /**
     * Relation vers les nœuds de l'organigramme utilisant ce format.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chartnodes() {
        return $this
            ->hasMany('App\Models\Chartnode');
    }

    /**
     * Accesseur générant une chaîne de styles CSS basée sur les attributs du format.
     *
     * @return string Style CSS inline.
     */
    public function getStyleAttribute() {
        return
            'border-radius : 0.375rem; '.
            'border-style : '.($this->border_style ?: 'none').'; '.
            ($this->bg_color ? AP::getFormatBgColors()[$this->bg_color] : '');
    }

    /**
     * Filtre les formats selon un critère de recherche.
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $formats = static::query()
            ->get()
            ->when($search, function ($formats) use ($search) {
                return $formats->filter(function ($format) use ($search) {
                    return static::tableContains([
                        $format->name,
                    ], $search);
                });
            });

        return $formats->isEmpty() ? static::whereNull('id') : $formats->toQuery();
    }
}
