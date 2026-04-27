<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;

/**
 * Composant Livewire pour la gestion et l'affichage des notifications de l'utilisateur.
 */
class NotificationsManager extends Component
{
    /**
     * Nombre maximum de notifications à afficher au total.
     *
     * @var int
     */
    public $nbMaxNotif = 20;

    /**
     * Marque une notification comme consommée en la détachant de l'utilisateur.
     *
     * @param int $notificationId Identifiant de la notification.
     */
    public function consumedNotif($notificationId) {
        auth()->user()->newNotifications()->detach($notificationId);
    }

    /**
     * Rendu du composant.
     * Récupère les notifications récentes et anciennes de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $newNotifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', today())
            ->sortByDesc('release_at')
            ->sortByDesc('created_at');

        $nbMaxOldNotif = $this->nbMaxNotif > $newNotifications->count() ?
            $this->nbMaxNotif - $newNotifications->count() : 0;

        return view('livewire.modals.usage.notifications-manager', [
            'newNotifications' => $newNotifications,
            'oldNotifications' => auth()->user()
                ->oldNotifications()
                ->sortByDesc('release_at')
                ->sortByDesc('created_at')
                ->take($nbMaxOldNotif),
        ]);
    }
}
