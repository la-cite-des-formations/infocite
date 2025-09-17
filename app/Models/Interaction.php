<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    // Table explicitement définie
    protected $table = 'interactions';

    protected $casts = [
        'interaction_at' => 'date:Y-m-d',
    ];

    // Pas de timestamps automatiques
    public $timestamps = false;

    // Mass assignable
    protected $fillable = [
        'user_id',
        'target_id',
        'target_type',
        'type',
        'interaction_at',
    ];

    /**
     * L'utilisateur qui a effectué l'interaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Le modèle cible associé à l'interaction (polymorphique)
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope pour récupérer les interactions d'un utilisateur (objet ou id)
     */
    public function scopeByUser($query, $user)
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour récupérer les interactions des utilisateurs d'un certain type
     */
    public function scopeByUserType($query, ?string $userType)
    {
        return match ($userType) {
            'staff', 'employees' => $query->join('employees', 'employees.user_id', '=', 'interactions.user_id'),
            'learner', 'learners' => $query->join('learners', 'learners.user_id', '=', 'interactions.user_id'),
            default => $query,
        };
    }

    /**
     * Scope pour récupérer les interactions avec modèle cible associé
     */
    public function scopeWithTarget($query, Model $target)
    {
        return $query
            ->where('target_type', get_class($target))
            ->where('target_id', $target->getKey());
    }

    /**
     * Scope pour récupérer les interactions sans modèle cible associé
     */
    public function scopeWithoutTarget($query)
    {
        return $query
            ->whereNull('target_type')
            ->whereNull('target_id');
    }

    /**
     * Scope pour récupérer les interactions d'un type en particulier
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour récupérer les interactions d'une date en particulier
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('interaction_at', $date);
    }

    /**
     * Scope pour récupérer les interactions d'une période (entre 2 dates)
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('interaction_at', [$start, $end]);
    }

    /**
     * Scope pour récupérer les interactions d'une année scolaire donnée
     */
    public function scopeForSchoolYear($query, $startYear)
    {
        $start = Carbon::create($startYear, 9, 1)->startOfDay();  // 1er sept
        $end   = Carbon::create($startYear + 1, 8, 31)->endOfDay(); // 31 août

        return $query->betweenDates($start, $end);
    }

    /**
     * Vérifie l'existance d'une interaction à une certaine date
     * Procède à son enregistrement si nécessaire
     */
    public static function ensure(string $type, ?Model $target = null, ?int $userId = null, $date = null): self
    {
        $userId = $userId ?? auth()->id();

        return static::firstOrCreate([
            'type' => $type,
            'user_id' => $userId,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->getKey(),
            'interaction_at' => $date ?? today(),
        ]);
    }
}
