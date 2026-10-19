<?php

namespace App\Models;

use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente un type d'événement dans l'agenda.
 */
class EventType extends Model
{
    use WithSearching;

    protected $fillable = ['name', 'color'];

    /**
     * Relation vers les événements associés.
     */
    public function events() {
        return $this->hasMany(Event::class, 'event_type_id');
    }

    /**
     * Filtre les types d'événements selon un critère de recherche.
     *
     * @param array $filter Critères de filtrage.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filter(array $filter) {
        extract($filter);

        $eventTypes = static::query()
            ->get()
            ->when($search, function ($types) use ($search) {
                return $types->filter(function ($type) use ($search) {
                    return static::tableContains([
                        $type->name,
                    ], $search);
                });
            });

        return $eventTypes->isEmpty() ? static::whereNull('id') : $eventTypes->toQuery();
    }
}
