<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Représente le profil "Apprenant" d'un utilisateur.
 * Contient des informations personnelles liées à la scolarité.
 */
class Learner extends Model
{
    /** @var string Nom de la table associée. */
    protected $table = 'learners';

    /** @var string Nom de la clé primaire. */
    protected $primaryKey = 'user_id';

    /** @var bool Désactive l'auto-incrémentation. */
    public $incrementing = FALSE;

    /** @var string Type de la clé primaire. */
    protected $keyType = 'int';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'birthday',
        'gender',
        'language',
        'status',
        'quality',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date:Y-m-d',
    ];

    /** @var bool Désactive les timestamps automatiques. */
    public $timestamps = FALSE;

    /**
     * Relation vers le compte utilisateur associé.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(User::class);
    }
}
