<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    const MESSAGES = [
        'NP' => 'Nouvel article disponible : ',
        'UP' => 'Article mis à jour le @date : ',
        'CP' => 'Article commenté : ',
        'NA' => 'Nouvelle application disponible : ',
        'UA' => 'Application mise à jour : ',
        'UO' => 'Organigramme mis à jour : ',
        'DF' => 'Information : ',
    ];

    protected $data;

    /**
     * Créez une nouvelle instance de notification.
     *
     * @param array $data  Exemple : ['title' => 'Titre', 'body' => 'Contenu', 'icon' => '/path/to/icon.png']
     */
    public function __construct(array $data)
    {
        $type = $data['type'] ?? 'DF';
        $post = $data['post'] ?? NULL;
        $body = $data['body'] ?? '';

        $message = str_replace(
            '@date',
            today()->format('d/m/Y'),
            in_array($type, array_keys(self::MESSAGES)) ? self::MESSAGES[$type] : self::MESSAGES['DF']
        );

        switch (TRUE) {
            case in_array($type, ['NP', 'UP', 'CP']):
                $this->data['url'] = config('app.url') . ($post ? $post->route : '');
                $message .= ($post ? $post->title : '');
                break;

            case in_array($type, ['NA', 'UA']):
                $this->data['url'] = config('app.url') . '#apps';
                break;

            case $type === 'UO':
                $this->data['url'] = config('app.url') . '/rh.org-chart';
                break;

            default :
                $this->data['url'] = $data['url'] ?? config('app.url');
        }

        $this->data['title'] = $data['title'] ?? config('app.name');
        $this->data['body'] = $message . $body;
        $this->data['icon'] = $data['icon'] ?? config('app.icon');
    }

    /**
     * Détermine les canaux par lesquels la notification sera envoyée.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->can('receiveDesktopNotifs') ? ['fcm'] : [];
    }

    public function getData() {
        return $this->data;
    }
}
