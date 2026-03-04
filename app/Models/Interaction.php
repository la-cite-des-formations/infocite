<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Représente une interaction utilisateur au sein du système.
 * Utilisé pour le suivi d'activité (log de connexions, vues, actions).
 */
class Interaction extends Model
{
    /** @var string Nom de la table associée. */
    protected $table = 'interactions';

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    /** @var bool Désactive les timestamps automatiques (created_at, updated_at). */
    public $timestamps = false;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'target_id',
        'target_type',
        'type',
        'occurred_at',
    ];

    /**
     * Relation vers l'utilisateur ayant effectué l'interaction.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation polymorphique vers le modèle cible de l'interaction.
     *
     * @return MorphTo
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope pour filtrer les interactions par utilisateur.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User|int $user Instance d'utilisateur ou ID.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $user)
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer les interactions par type d'utilisateur (employé ou apprenant).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $userType Type d'utilisateur ('staff', 'employees', 'learner', 'learners').
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserType($query, ?string $userType)
    {
        return match ($userType) {
            'staff', 'employees' => $query->whereHas('user.employee'),
            'learner', 'learners' => $query->whereHas('user.learner'),
            default => $query,
        };
    }

    /**
     * Scope pour filtrer par type de modèle cible (nom de classe).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $targetType Nom complet de la classe cible.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTargetType($query, $targetType)
    {
        return $query
            ->where('target_type', $targetType);
    }

    /**
     * Scope pour filtrer par une instance spécifique de modèle cible.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Model $target Instance du modèle cible.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTarget($query, Model $target)
    {
        return $query
            ->where('target_type', get_class($target))
            ->where('target_id', $target->getKey());
    }

    /**
     * Scope pour filtrer les interactions sans cible spécifique.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTarget($query)
    {
        return $query
            ->whereNull('target_type')
            ->whereNull('target_id');
    }

    /**
     * Scope pour filtrer par type d'interaction.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type Type d'interaction.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par une liste de types d'interactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<string> $types Liste de types.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfTypes($query, array $types)
    {
        return $query->whereIn('type', $types);
    }

    /**
     * Scope pour filtrer par date d'occurrence (ignore l'heure).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $date Date.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('occurred_at', $date);
    }

    /**
     * Scope pour filtrer par date et heure précises.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $datetime Date et heure.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDatetime($query, $datetime)
    {
        return $query->where('occurred_at', Carbon::parse($datetime));
    }

    /**
     * Scope pour filtrer sur une période donnée.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $start Date de début.
     * @param mixed $end Date de fin.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('occurred_at', [$start, $end]);
    }

    /**
     * Scope pour filtrer sur une année scolaire (du 1er sept au 31 août).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $startYear Année de début (ex: 2023 pour 2023-2024).
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSchoolYear($query, $startYear)
    {
        $start = Carbon::create($startYear, 9, 1)->startOfDay();  // 1er sept
        $end   = Carbon::create($startYear + 1, 8, 31)->endOfDay(); // 31 août

        return $query->betweenDates($start, $end);
    }

    /**
     * Assure l'enregistrement d'une interaction.
     * Utilise firstOrCreate pour éviter les doublons sur les mêmes critères.
     *
     * @param string $type Type d'interaction.
     * @param mixed|null $datetime Moment de l'interaction (default: now).
     * @param Model|null $target Modèle cible éventuel.
     * @param int|null $userId ID de l'utilisateur (default: utilisateur authentifié).
     * @return self
     */
    public static function ensure(string $type, $datetime = null, ?Model $target = null, ?int $userId = null): self
    {
        $userId = $userId ?? auth()->id();

        return static::firstOrCreate([
            'type' => $type,
            'user_id' => $userId,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->getKey(),
            'occurred_at' => $datetime ?? today(),
        ]);
    }
}
