<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Représente un numéro de téléphone associé à un utilisateur.
 */
class Phone extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'number',
        'target',
    ];

    /**
     * Relation vers l'utilisateur propriétaire du numéro.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
