<?php

namespace App\Channels;

use App\Models\FcmToken;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Exception\MessagingException;

class FcmChannel
{
    protected $messaging;

    /**
     * Injecter le service Messaging via le conteneur de services.
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Envoi de la notification via Firebase.
     *
     * @param mixed $notifiable
     * @param Notification $notification
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
        $message = CloudMessage::new()->withData($notification->getData() ?? []);

        // Envoi du message à tous les tokens
        try {
            $report = $this->messaging->sendMulticast($message, $tokens->toArray());

            $invalidTokens = $report->invalidTokens();

            if (!empty($invalidTokens)) {
                \Log::info('Nettoyage de ' . count($invalidTokens) . ' tokens invalides après envoi.');
                // Supprimer ces tokens de la base de données en une seule requête
                FcmToken::whereIn('token', $invalidTokens)->delete();
            }
        } catch (MessagingException $e) {
            \Log::error("Erreur MessagingException lors de l'envoi de la notification FCM : " . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error("Erreur générale lors de l'envoi de la notification FCM : " . $e->getMessage());
        }
    }
}
