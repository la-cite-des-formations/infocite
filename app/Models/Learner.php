<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    protected $table = 'learners';

    protected $primaryKey = 'user_id';
    public $incrementing = FALSE;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'birthday',
        'gender',
        'language',
        'status',
        'quality',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthday' => 'date:Y-m-d',
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
}
