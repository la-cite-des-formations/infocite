<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notification système personnalisée pour le portail (Push FCM).
 * Gère la construction des messages dynamiques selon le type (Article, Application, Organigramme).
 */
class AppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var array<string, string> Libellés de base des messages par type de notification. */
    const MESSAGES = [
        'NP' => 'Nouvel article disponible : ',
        'UP' => 'Article mis à jour le @date : ',
        'CP' => 'Article commenté : ',
        'NA' => 'Nouvelle application disponible : ',
        'UA' => 'Application mise à jour : ',
        'UO' => 'Organigramme mis à jour : ',
        'DF' => 'Information : ',
    ];

    /** @var array Stockage des données de la notification (url, titre, corps, icône). */
    protected $data;

    /**
     * Crée une nouvelle instance de notification.
     * Initialise les données dynamiques et construit le message final.
     *
     * @param array $data Données sources (type, post, body, title, url, icon).
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
     * Envoie via FCM si l'utilisateur a autorisé les notifications de bureau.
     *
     * @param mixed $notifiable L'entité notifiée (User).
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->can('receiveDesktopNotifs') ? ['fcm'] : [];
    }

    /**
     * Retourne les données construites de la notification.
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }
}
