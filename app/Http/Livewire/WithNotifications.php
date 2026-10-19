<?php

namespace App\Http\Livewire;

/**
 * Trait pour la gestion des notifications utilisateur dans les composants Livewire.
 */
trait WithNotifications
{
    /**
     * Collection des notifications de l'utilisateur.
     *
     * @var \Illuminate\Support\Collection
     */
    public $notifications;

    /**
     * Rappels (callbacks) à exécuter à la fermeture de la modale.
     *
     * @var array
     */
    protected $closedModalCallback = ['detachNotifications', 'setNotifications'];

    /**
     * Initialise la liste des nouvelles notifications non lues.
     *
     * @param string|null $modal Nom du composant modal fermé.
     */
    public function setNotifications($modal = null) {
        if (!auth()->check()) {
            $this->notifications = collect();
            return;
        }

        auth()->user()->unsetRelation('newNotifications');
        $this->notifications = auth()->user()
            ->newNotifications()
            ->where('notifications.release_at', '<=', now())
            ->get();
    }

    /**
     * Rafraîchit la collection de notifications non lues (écouteur d'événement Livewire).
     */
    public function refreshNotifications() {
        $this->setNotifications();
    }

    /**
     * Marque les notifications comme lues en les détachant de l'utilisateur.
     * Exception : les notifications de type 'RP' (révision d'articles) ne sont pas détachées
     * automatiquement à la fermeture de la modale. Elles persistent tant que l'utilisateur
     * a des articles obsolètes à arbitrer dans la section "Mes articles à réviser".
     *
     * @param string|null $modal Nom du composant modal fermé.
     */
    public function detachNotifications($modal = null) {
        if ($modal && $modal !== 'usage.notifications-manager') {
            return;
        }

        $notificationsIds = $this->notifications
            ->reject(fn($n) => $n->content_type === 'RP')
            ->pluck('id');

        auth()->user()->newNotifications()->detach($notificationsIds);
        auth()->user()->unsetRelation('newNotifications');
    }
}
