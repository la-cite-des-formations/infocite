<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente un événement de l'agenda, autonome ou associé à un article.
 */
class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
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
        'image' => 'array',
    ];

    /**
     * Relation vers l'article associé.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relation vers le type d'événement.
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    /**
     * Accesseur pour le titre avec repli sur le post lié.
     */
    public function getTitleAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return $this->post?->title ?? '';
    }

    /**
     * Accesseur pour la description avec repli sur le post lié.
     */
    public function getDescriptionAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return $this->post?->content ? strip_tags($this->post->content) : '';
    }

    /**
     * Retourne l'URL de la vignette de l'événement ou de l'article lié.
     *
     * @return string|null
     */
    public function getThumbnailUrl(): ?string
    {
        if (!empty($this->image) && is_array($this->image)) {
            return $this->image['thumbnail'] ?? $this->image['path'] ?? null;
        }

        if ($this->post && $this->post->gallery) {
            return $this->post->gallery->firstThumbnail();
        }

        return null;
    }

    /**
     * Retourne le point focal de l'image (défaut 50/50).
     *
     * @return array{x: float, y: float}
     */
    public function getFocalPoint(): array
    {
        $fp = $this->image['focal_point'] ?? null;
        return [
            'x' => isset($fp['x']) ? (float) $fp['x'] : 50.0,
            'y' => isset($fp['y']) ? (float) $fp['y'] : 50.0,
        ];
    }

    /**
     * Retourne le style CSS du point focal pour object-position ou background-position.
     *
     * @return string
     */
    public function focalPointStyle(): string
    {
        $fp = $this->getFocalPoint();
        return "object-position: {$fp['x']}% {$fp['y']}%; background-position: {$fp['x']}% {$fp['y']}%;";
    }

    /**
     * Scope de filtrage des événements (utilisé en front et en administration).
     *
     * @param Builder $query
     * @param array $filter
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filter)
    {
        if (!empty($filter['event_type_id'])) {
            $query->where('event_type_id', $filter['event_type_id']);
        }

        if (!empty($filter['start_date'])) {
            $startDate = $filter['start_date'];
            $query->where(function ($q) use ($startDate) {
                $q->where('start_date', '>=', $startDate)
                  ->orWhere(function ($q2) use ($startDate) {
                      $q2->whereNotNull('end_date')
                         ->where('end_date', '>=', $startDate);
                  });
            });
        }

        if (!empty($filter['end_date'])) {
            $query->where('start_date', '<=', $filter['end_date']);
        }

        if (!empty($filter['search'])) {
            $search = '%' . trim($filter['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search)
                  ->orWhere('location', 'like', $search)
                  ->orWhereHas('post', function ($postQ) use ($search) {
                      $postQ->where('title', 'like', $search)
                            ->orWhere('content', 'like', $search);
                  });
            });
        }

        return $query;
    }
}
