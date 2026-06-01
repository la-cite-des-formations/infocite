<?php

namespace App\Channels;

use App\Models\FcmToken;
use App\Notifications\AppNotification;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;

/**
 * Canal de notification personnalisé pour Firebase Cloud Messaging (FCM).
 * Gère l'envoi multicast des messages et le nettoyage automatique des tokens invalides.
 */
class FcmChannel
{
    /** @var Messaging Service de messagerie Firebase. */
    protected $messaging;

    /**
     * Initialise le canal avec le service Messaging.
     *
     * @param Messaging $messaging Le service Firebase injecté.
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Envoie la notification à tous les tokens FCM de l'utilisateur.
     * Prépare le message multicast, traite le rapport d'envoi et supprime les tokens expirés.
     *
     * @param mixed $notifiable L'entité notifiée (User).
     * @param AppNotification|Notification $notification La notification à envoyer.
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Vérifie l'existence d'un token FCM pour l'utilisateur
        $tokens = $notifiable->fcmTokens()->pluck('token')->filter()->all();

        if (empty($tokens)) {
            return;
        }

        // Prépare le message à diffuser sur tous les tokens
        // On utilise getData() qui est spécifique à AppNotification
        $data = method_exists($notification, 'getData') ? $notification->getData() : [];
        $message = CloudMessage::new()->withData($data);

        // Envoi du message à tous les tokens
        try {
            $report = $this->messaging->sendMulticast($message, $tokens);

            $invalidTokens = $report->invalidTokens();

            if (!empty($invalidTokens)) {
                Log::info('Nettoyage de ' . count($invalidTokens) . ' tokens invalides après envoi.');
                // Supprimer ces tokens de la base de données en une seule requête
                FcmToken::whereIn('token', $invalidTokens)->delete();
            }
        } catch (MessagingException $e) {
            Log::error("Erreur MessagingException lors de l'envoi de la notification FCM : " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Erreur générale lors de l'envoi de la notification FCM : " . $e->getMessage());
        }
    }
}
