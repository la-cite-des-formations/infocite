<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarTraining extends Model
{
    protected $table = 'star_trainings';

    protected $fillable = [
        'code_training',
        'name_training',
        'sector_id',
        'degree_id',
    ];

    public function sector()
    {
        return $this->belongsTo(StarSector::class, 'sector_id');
    }

    public function degree()
    {
        return $this->belongsTo(StarDegree::class, 'degree_id');
    }

    public function students()
    {
        return $this->hasMany(StarStudient::class, 'training_id');
    }
}
