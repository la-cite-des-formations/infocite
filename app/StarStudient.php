<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarStudient extends Model
{
    protected $table = 'star_studients';

    protected $fillable = [
        'code_apprenant',
        'birthday',
        'gender',
        'level_id',
        'training_id',
        'adress',
        'city',
        'postal_code',
        'family_situation',
        'language',
        'quality',
        'status',
        'attendance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'code_apprenant', 'code_ypareo');
    }

    public function level()
    {
        return $this->belongsTo(StarLevel::class, 'level_id');
    }

    public function training()
    {
        return $this->belongsTo(StarTraining::class, 'training_id');
    }
}
