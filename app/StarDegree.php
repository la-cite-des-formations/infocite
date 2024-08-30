<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarDegree extends Model
{
    protected $table = 'star_degrees';

    protected $fillable = [
        'code_degree',
        'name',
    ];

    public function trainings()
    {
        return $this->hasMany(StarTraining::class, 'degree_id');
    }
}
