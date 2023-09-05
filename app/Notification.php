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
    protected $fillable = ['post_id', 'content_type', 'release_at'];
    protected $casts = ['release_at' => 'date:Y-m-d'];

    public function getMessageAttribute() {
        $message = AP::getNotifications($this->content_type);

        if ($this->content_type == 'UP') {
            $message = str_replace('@date', $this->release_at->format('d/m/Y'), $message);
        }

        return $message;
    }

    public function getHRefAttribute() {
        switch ($this->content_type) {
            case 'NP' :
            case 'UP' :
            case 'CP' :
                return $this->post->route;
            case 'NA' :
            case 'UA' :
                return '#apps';
            case 'UO' :
                return 'rh.org-chart';
        }
    }

    public function users() {
        return $this
            ->belongsToMany('App\User');
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function rubric()
    {
        return $this->hasOneThrough('App\Rubric', 'App\Post');
    }
}
