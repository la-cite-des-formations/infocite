<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class InfociteNotification extends Notification
{
    protected $data;

    /**
     * Créez une nouvelle instance de notification.
     *
     * @param array $data  Exemple : ['title' => 'Titre', 'body' => 'Contenu', 'icon' => '/path/to/icon.png']
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Détermine les canaux par lesquels la notification sera envoyée.
     *
     * Ici, on retourne le canal personnalisé 'firebase'.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Prépare le message pour le canal Firebase.
     *
     * @param mixed $notifiable
     * @return CloudMessage
     */
    public function toFirebase($notifiable)
    {
        // On suppose que chaque utilisateur possède un attribut fcm_token (ou via une méthode de routage)
        $token = $notifiable->routeNotificationFor('firebase');

        return CloudMessage::new()
            ->toToken($token)
            ->withData([
                'icon'  => $this->data['icon'] ?? env('APP_FAVICON'),
                'title' => $this->data['title'] ?? env('APP_NAME'),
                'body'  => $this->data['body'],
            ]);
    }
}
