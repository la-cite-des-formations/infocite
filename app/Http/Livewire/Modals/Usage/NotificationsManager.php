<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;
use App\Models\Notification;

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
     * Si c'est une notification de rappel des modèles ('DF'), active le mode édition en session.
     *
     * @param int $notificationId Identifiant de la notification.
     */
    public function consumedNotif($notificationId) {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->content_type === 'DF') {
            session(['mode' => 'edition']);
        }

        auth()->user()->newNotifications()->detach($notificationId);
        auth()->user()->unsetRelation('newNotifications');
    }

    /**
     * Active le mode édition en session (utilisé lors du clic sur les notifications de modèles).
     */
    public function setEditionMode() {
        session(['mode' => 'edition']);
    }

    /**
     * Active le mode édition en session, détache la notification si nécessaire
     * et redirige de façon transparente vers la Une (sans paramètre d'URL).
     *
     * @param int|null $notificationId
     */
    public function openTemplateReminder($notificationId = null) {
        if ($notificationId) {
            $this->consumedNotif($notificationId);
        }
        session(['mode' => 'edition']);
        return redirect()->to('/une');
    }

    /**
     * Rendu du composant.
     * Récupère les notifications récentes et anciennes de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        auth()->user()->unsetRelation('newNotifications');
        $newNotifications = auth()->user()
            ->newNotifications
            ->where('release_at', '<=', now())
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
