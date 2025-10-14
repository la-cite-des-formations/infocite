<?php

namespace App\Http\Livewire;

use App\Models\FcmToken;
use Livewire\Component;

class FcmNotifsSwClientManager extends Component
{
    use WithModal;

    public $currentRoute;

    protected $closedModalCallback = ['guidelinesRead'];
    protected $listeners = ['showModal', 'modalClosed', 'traitFcmToken', 'registerNotificationPermission'];

    public function mount($viewBag) {
        $this->currentRoute = $viewBag->currentRoute ?? '/une';
    }

    public function guidelinesRead() {
        $this->emit('guidelinesRead', 'desktop-notifications');
    }

    public function traitFcmToken($token) {
        // si besoin, enregistrement du token et association à l'utilisateur courant
        $currentUser = auth()->user();
        $tokenRecord = FcmToken::firstOrCreate(['token' => $token]);

        if ($currentUser->employee && !$currentUser->fcmTokens()->where('token', $token)->exists()) {
            $currentUser->fcmTokens()->attach($tokenRecord->id);
        }

        // si non défini, enregistrement du token comme token par défaut de l'utilisateur courant
        if ($currentUser->employee && !$currentUser->employee->default_fcm_token_id) {
            $currentUser->employee->default_fcm_token_id = $tokenRecord->id;
            $currentUser->employee->save();
        }
    }

    public function registerNotificationPermission($permission) {
        auth()->user()->employee?->update(['desktop_notifications_granted' => $permission !== 'denied']);

        $this->emit('refreshPermissionSwitch', $permission);

        if ($permission === 'default') {
            redirect()->to($this->currentRoute);
        }
    }

    public function render() {
        return view('livewire.fcm-notifs-sw-client-manager');
    }
}
