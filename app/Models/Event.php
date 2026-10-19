<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Représente un événement de l'agenda associé à un article.
 */
class Event extends Model
{
    protected $fillable = [
        'post_id',
        'event_type_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'location',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    /**
     * Relation vers l'article associé.
     */
    public function post() {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relation vers le type d'événement.
     */
    public function eventType() {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
}
