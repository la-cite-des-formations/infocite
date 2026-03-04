<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Représente le profil "Employé" d'un utilisateur.
 * Contient des informations professionnelles et des préférences de notification.
 */
class Employee extends Model
{
    /** @var string Nom de la table associée. */
    protected $table = 'employees';

    /** @var string Nom de la clé primaire. */
    protected $primaryKey = 'user_id';

    /** @var bool Désactive l'auto-incrémentation de la clé primaire. */
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
        'desktop_notifications_granted',
        'notify_only_favorites',
        'default_fcm_token_id',
        'position',
        'building',
        'office',
    ];

    /**
     * Indique si le modèle doit avoir des timestamps.
     *
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * Accesseur retournant la localisation de l'employé (Bâtiment - Bureau).
     *
     * @return string
     */
    public function getLocationAttribute() {
        $items = [];

        if ($this->building) $items[] = $this->building;
        if ($this->office) $items[] = $this->office;

        return implode(' - ', $items);
    }

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
