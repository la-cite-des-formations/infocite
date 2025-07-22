<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $primaryKey = 'user_id';
    public $incrementing = FALSE;
    protected $keyType = 'int';

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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * Compte utilisateur associé
     */
    public function account()
    {
        return $this->belongsTo(User::class);
    }

    public function fcmTokens() {
        return $this->belongsToMany(FcmToken::class);
    }
}
