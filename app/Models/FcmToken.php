<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;

class FcmToken extends Model
{
    protected $table = 'fcm_tokens';
    protected $fillable = ['token', 'browser', 'computer_id'];
    protected $casts = [];

    public function users() {
        return $this->belongsToMany(Employee::class);
    }

    public function isMine() {
        return $this->users?->contains('user_id', auth()->user()->id);
    }

    public function getIsInvalidAttribute()
    {
        // Instancier Firebase avec votre fichier de credentials
        $firebase = (new Factory)
            ->withServiceAccount(storage_path(config('firebase.credentials')));

        $messaging = $firebase->createMessaging();

        // Créer un message de test (dry-run)
        $message = CloudMessage::new()
            ->toToken($this->token)
            ->withData(['test' => 'dry-run']);

        try {
            // En mode dry-run, le message n'est pas réellement envoyé
            $messaging->send($message, true);
            // Si aucun message d'erreur n'est levé, le token est considéré valide
            return FALSE;
        } catch (MessagingException $e) {
            // Ici, on peut analyser $e->getMessage() pour voir s'il s'agit d'une erreur liée au token
            \Log::error("Token invalide : " . $e->getMessage());
            return TRUE;
        } catch (\Exception $e) {
            // Pour toutes autres exceptions, on loggue et on considère le token potentiellement invalide
            \Log::error("Erreur lors de la vérification du token : " . $e->getMessage());
            return TRUE;
        }
    }

    public static function purgeInvalidTokens() {
        static::all()
            ->each(function ($fcmToken) {
                if ($fcmToken->isInvalid) {
                    $fcmToken->delete();
                }
            });
    }
}
