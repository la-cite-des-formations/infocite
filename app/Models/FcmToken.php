<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $table = 'fcm_tokens';
    protected $fillable = ['token', 'browser', 'computer_id'];
    protected $casts = [];

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function isMine() {
        return $this->users()->where('user_id', auth()->id())->exists();
    }
}
