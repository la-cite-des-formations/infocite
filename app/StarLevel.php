<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarLevel extends Model
{
    protected $table = 'star_levels';

    protected $fillable = [
        'code_level',
        'name',
    ];

    public function students()
    {
        return $this->hasMany(StarStudient::class, 'level_id');
    }
}
