<?php

namespace App\Http\Livewire;

use App\Models\FcmToken;
use Livewire\Component;

/**
 * Composant Livewire gérant l'enregistrement des jetons FCM (Firebase Cloud Messaging)
 * et les permissions de notifications push côté client.
 */
class FcmNotifsSwClientManager extends Component
{
    use WithModal;

    /**
     * Route actuelle pour redirection.
     *
     * @var string
     */
    public $currentRoute;

    protected $closedModalCallback = ['guidelinesRead'];
    protected $listeners = ['showModal', 'modalClosed', 'traitFcmToken', 'registerNotificationPermission'];

    /**
     * Initialisation du composant.
     *
     * @param \Illuminate\Http\Request $viewBag Contient la route actuelle.
     */
    public function mount($viewBag) {
        $this->currentRoute = $viewBag->currentRoute ?? '/une';
    }

    /**
     * Signale que les guides d'utilisation ont été lus.
     */
    public function guidelinesRead() {
        $this->emit('guidelinesRead', 'desktop-notifications');
    }

    /**
     * Traite et enregistre le jeton FCM reçu du client.
     *
     * @param string $token Jeton unique Firebase.
     */
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

        // On informe le client que l'enregistrement est un succès
        // pour qu'il puisse le stocker dans son localStorage.
        $this->emit('fcmTokenSaved', $token);
    }

    /**
     * Enregistre le choix de l'utilisateur concernant les permissions de notifications de bureau.
     *
     * @param string $permission État de la permission (granted, denied, default).
     */
    public function registerNotificationPermission($permission) {
        auth()->user()->employee?->update(['desktop_notifications_granted' => $permission !== 'denied']);

        $this->emit('refreshPermissionSwitch', $permission);

        if ($permission === 'default') {
            redirect()->to($this->currentRoute);
        }
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.fcm-notifs-sw-client-manager');
    }
}
