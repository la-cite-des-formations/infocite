<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    /** @var bool Désactivation des timestamps. */
    public $timestamps = false;

    /** @var bool Désactivation de l'auto-incrémentation pour la clé composite. */
    public $incrementing = false;

    /** @var array Attributs assignables en masse. */
    protected $fillable = ['user_id', 'favoriteable_id', 'favoriteable_type', 'rank'];

    /**
     * Relation polymorphique vers le modèle favorité (Rubric, Post, App).
     */
    public function favoriteable()
    {
        return $this->morphTo();
    }

    /**
     * Relation vers l'utilisateur propriétaire du favori.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
