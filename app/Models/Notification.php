<?php

namespace App\Models;

use App\Notifications\AppNotification;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    protected $fillable = ['object_type', 'object_id', 'content_type', 'release_at'];
    protected $casts = ['release_at' => 'date:Y-m-d'];

    public function getMessageAttribute() {
        return str_replace('@date', $this->release_at->format('d/m/Y'), AppNotification::MESSAGES[$this->content_type]);
    }

    public function getHRefAttribute() {
        switch ($this->content_type) {
            case 'NP' :
            case 'UP' :
            case 'CP' :
                return $this->object->route;
            case 'NA' :
            case 'UA' :
                return '#apps';
            case 'UO' :
                return 'rh.org-chart';
        }
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function object() {
        return $this->morphTo();
    }
}
