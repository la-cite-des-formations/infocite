<?php

namespace App\Models;

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
            ->belongsTo('App\Models\User');
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

    public static function fromDate(Carbon $date, $filter = []) {
        extract($filter);
        $isStaff = !isset($userType) || ($userType == 'all') ? NULL : $userType == 'staff';

        return static::query()
            ->when($isStaff !== NULL, function ($query) use ($isStaff) {
                $query
                    ->join('users', 'connections.user_id', '=', 'users.id')
                    ->where('users.is_staff', $isStaff);
            })
            ->where('connected_at', $date->format('Y-m-d'));
    }

    public static function allGroupByDate($filter = []) {
        extract($filter);
        $isStaff = !isset($userType) || ($userType == 'all') ? NULL : $userType == 'staff';

        return static::query()
            ->when($isStaff !== NULL, function ($query) use ($isStaff) {
                $query
                    ->join('users', 'connections.user_id', '=', 'users.id')
                    ->where('users.is_staff', $isStaff);
            })
            ->selectRaw('connected_at, count(*) as connections_nb')
            ->orderBy('connected_at', 'desc')
            ->groupBy('connected_at');
    }
}
