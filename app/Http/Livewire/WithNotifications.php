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
        auth()->user()->unsetRelation('newNotifications');
        $this->notifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', now());
    }

    /**
     * Marque les notifications comme lues en les détachant de l'utilisateur.
     *
     * @param string|null $modal Nom du composant modal fermé.
     */
    public function detachNotifications($modal = null) {
        if ($modal && $modal !== 'usage.notifications-manager') {
            return;
        }

        $notificationsIds = $this->notifications->pluck('id');

        auth()->user()->newNotifications()->detach($notificationsIds);
        auth()->user()->unsetRelation('newNotifications');
    }
}
