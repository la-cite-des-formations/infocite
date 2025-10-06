<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'number',
        'target',
    ];

    /**
     * La relation vers l'utilisateur propriétaire
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
