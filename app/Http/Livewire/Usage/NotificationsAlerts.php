<?php

namespace App\Http\Livewire\Usage;

use App\Models\Notification;
use Livewire\Component;

/**
 * Composant Livewire pour l'affichage des alertes refermables en haut de page.
 * Affiche les alertes prioritaires d'obsolescence des contenus (type 'RP')
 * et de rappel trimestriel de l'usage des modèles d'articles (type 'DF').
 */
class NotificationsAlerts extends Component
{
    protected $listeners = ['refreshAlerts' => '$refresh'];

    /**
     * Rendu du composant.
     * Récupère les notifications actives de type 'RP' et 'DF' non encore écartées durant la session.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        if (!auth()->check()) {
            return view('livewire.usage.notifications-alerts', [
                'alerts' => collect(),
            ]);
        }

        $user = auth()->user();
        $dismissedAlertIds = session('dismissed_alerts', []);

        $alerts = $user->newNotifications()
            ->whereIn('content_type', ['RP', 'DF'])
            ->where('release_at', '<=', now())
            ->get()
            ->reject(fn($n) => in_array($n->id, $dismissedAlertIds));

        return view('livewire.usage.notifications-alerts', [
            'alerts' => $alerts,
        ]);
    }

    /**
     * Ferme l'alerte pour la session courante.
     * Pour les notifications de rappel de modèles ('DF'), détache également la notification.
     *
     * @param int $notificationId
     */
    public function dismiss(int $notificationId)
    {
        $dismissed = session('dismissed_alerts', []);
        $dismissed[] = $notificationId;
        session(['dismissed_alerts' => array_unique($dismissed)]);

        $notification = Notification::find($notificationId);
        if ($notification && $notification->content_type === 'DF') {
            auth()->user()->newNotifications()->detach($notificationId);
            auth()->user()->unsetRelation('newNotifications');
        }

        $this->emit('refreshNotifications');
    }

    /**
     * Active le mode édition en session, détache la notification de rappel
     * et redirige de façon transparente vers la Une.
     *
     * @param int $notificationId
     */
    public function openTemplateReminder(int $notificationId)
    {
        $this->dismiss($notificationId);
        session(['mode' => 'edition']);
        return redirect()->to('/une');
    }
}
