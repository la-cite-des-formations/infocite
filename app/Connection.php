<?php

namespace App;

use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Connection extends Model
{
    use WithSearching;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'connected_at'];
    protected $casts = [
        'connected_at' => 'date:Y-m-d',
    ];
    public $timestamps = FALSE;

    public function user() {
        return $this
            ->belongsTo('App\User');
    }

    public function isMine() {
        return auth()->user()->id == $this->user_id;
    }

    public function isFromToday() {
        return $this->connected_at->format('Y-m-d') == today()->format('Y-m-d');
    }

    public static function fromToday() {
        return self::query()
            ->where('connected_at', today()->format('Y-m-d'));
    }

    public static function fromDate(Carbon $date) {
        return self::query()
            ->where('connected_at', $date->format('Y-m-d'));
    }

    public static function allGroupByDate() {
        return self::query()
            ->selectRaw('connected_at, count(*) as connections_nb')
            ->orderBy('connected_at', 'desc')
            ->groupBy('connected_at');
    }

    public static function filter(array $filter) {
        // TODO;
    }
}
