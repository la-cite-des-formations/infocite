<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarSector extends Model
{
    protected $table = 'star_sectors';

    protected $fillable = [
        'code_sector',
        'name',
    ];

    public function trainings()
    {
        return $this->hasMany(StarTraining::class, 'sector_id');
    }
}
