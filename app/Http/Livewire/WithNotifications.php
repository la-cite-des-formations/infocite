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
     */
    public function setNotifications() {
        $this->notifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', today());
    }

    /**
     * Marque les notifications comme lues en les détachant de l'utilisateur.
     */
    public function detachNotifications() {
        $notificationsIds = $this->notifications->pluck('id');

        auth()->user()->newNotifications()->detach($notificationsIds);
    }
}
