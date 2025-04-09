<?php

namespace App\Http\Livewire;

use App\Models\FcmToken;
use Livewire\Component;

class FcmTokenTrait extends Component
{
    use WithModal;

    protected $listeners = ['traitFcmToken'];

    public function mount() {
        //
    }

    public function traitFcmToken($token) {
        // purge des tokens invalides
        FcmToken::purgeInvalidTokens();

        // si besoin, enregistrement du token et association à l'utilisateur courant
        $currentUser = auth()->user();
        $tokenRecord = FcmToken::firstOrCreate(['token' => $token]);

        if (!$currentUser->fcmTokens()->where('token', $token)->exists()) {
            $currentUser->fcmTokens()->attach($tokenRecord->id);
        }

        // si non défini, enregistrement du token comme token par défaut de l'utilisateur courant
        if (!$currentUser->default_fcm_token_id) {
            $currentUser->default_fcm_token_id = $tokenRecord->id;
            $currentUser->save();
        }
    }

    public function render() {
        return view('livewire.fcm-token-trait');
    }
}
