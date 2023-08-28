<?php

namespace App;

use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['post_id', 'content_type'];
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
    public function rubric()
    {
        return $this->hasOneThrough('App\Rubric', 'App\Post');
    }
}
