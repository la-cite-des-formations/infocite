<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Représente un contexte d'aide en ligne (Guide en ligne).
 * Fait le lien entre un article (Post) et un contexte précis de l'interface utilisateur.
 * Permet l'affichage contextuel d'un guide via un bouton d'aide (icône '?') ou en ouverture
 * automatique lors de la première visite (onboarding).
 *
 * @property int         $id
 * @property int         $post_id           Référence vers l'article de guide.
 * @property string      $context_key       Identifiant unique du contexte (ex: 'desktop-notifications').
 * @property string|null $css_selector      Sélecteur CSS de l'élément cible pour mise en surbrillance.
 * @property string|null $next_context_key  Clé du contexte du guide de l'étape suivante (parcours guidé).
 * @property bool        $auto_open         Ouverture automatique à la première lecture.
 */
class Guideline extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'post_id',
        'context_key',
        'css_selector',
        'next_context_key',
        'auto_open',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'auto_open' => 'boolean',
    ];

    /**
     * Relation vers l'article de guide associé.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relation vers le contexte du guide suivant (pour les parcours guidés).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nextGuideline()
    {
        return $this->belongsTo(self::class, 'next_context_key', 'context_key');
    }

    /**
     * Récupère un guideline par sa clé de contexte.
     *
     * @param string $contextKey
     * @return static|null
     */
    public static function forContext(string $contextKey): ?static
    {
        return static::where('context_key', $contextKey)->with('post')->first();
    }
}
