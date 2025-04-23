<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;

class FcmChannel
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = (new Factory)
            ->withServiceAccount(storage_path(config('firebase.credentials')))
            ->createMessaging();
    }

    /**
     * Envoi de la notification via Firebase.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Vérifie l'existence d'un token FCM pour l'utilisateur
        $tokens = $notifiable->fcmTokens()->pluck('token')->filter();

        if ($tokens->isEmpty()) {
            return;
        }

        // Prépare le message à diffuser sur tous les tokens
        $message = CloudMessage::new()->withData($notification->getData() ?? []);

        // Envoi du message à tous les tokens
        try {
            $this->messaging->sendMulticast($message, $tokens->toArray());
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'envoi de la notification FCM : " . $e->getMessage());
        }
    }
}
