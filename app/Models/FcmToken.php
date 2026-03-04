<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Représente un token Firebase Cloud Messaging (FCM) associé à un ou plusieurs utilisateurs.
 * Utilisé pour les notifications push sur navigateurs et mobiles.
 */
class FcmToken extends Model
{
    /** @var string Nom de la table associée. */
    protected $table = 'fcm_tokens';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['token', 'browser', 'computer_id'];

    /** @var array<string, string> Les attributs qui doivent être castés. */
    protected $casts = [];

    /**
     * Relation vers les utilisateurs associés à ce token.
     * Un même ordinateur/navigateur peut être partagé par plusieurs comptes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(User::class);
    }

    /**
     * Vérifie si le token appartient à l'utilisateur authentifié.
     *
     * @return bool
     */
    public function isMine() {
        return $this->users()->where('user_id', auth()->id())->exists();
    }
}
