<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'user_id'];

    public function author() {
        return $this
            ->belongsTo('App\User', 'user_id');
    }

    public function isMine() {
        return auth()->user()->id == $this->user_id;
    }
}
