<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;

class FCMChannel
{
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
        if (!$notifiable->routeNotificationFor('firebase')) {
            return;
        }

        // Prépare le message via la méthode toFirebase définie dans la notification
        $message = $notification->toFirebase($notifiable);

        // Instanciation de Firebase avec le fichier de credentials
        $firebase = (new Factory)
            ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));

        $messaging = $firebase->createMessaging();

        // Envoi du message
        $messaging->send($message);
    }
}
