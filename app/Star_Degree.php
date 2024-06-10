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

    public function trainnings()
    {
        return $this->hasMany(StarTrainning::class, 'degree_id');
    }
}
