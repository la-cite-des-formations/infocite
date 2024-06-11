<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarTrainning extends Model
{
    protected $table = 'star_trainnings';

    protected $fillable = [
        'code_trainning',
        'name_trainning',
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
        return $this->hasMany(StarStudient::class, 'trainning_id');
    }
}
