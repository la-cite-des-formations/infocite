<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    protected $data;

    /**
     * Créez une nouvelle instance de notification.
     *
     * @param array $data  Exemple : ['title' => 'Titre', 'body' => 'Contenu', 'icon' => '/path/to/icon.png']
     */
    public function __construct(array $data)
    {
        $this->data = [
            'title' => $data['title'] ?? config('firebase.default_notification.title'),
            'body' => $data['body'] ?? config('firebase.default_notification.body'),
            'icon' => $data['icon'] ?? config('firebase.default_notification.icon'),
            'url' => $data['url'] ?? config('firebase.default_notification.url'),
        ];
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

    public function getData() {
        return $this->data;
    }
}
