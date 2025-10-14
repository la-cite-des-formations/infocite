<?php

namespace App\Jobs;

use App\Models\FcmToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;

class PurgeInvalidFcmTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Le nombre de tokens à traiter par lot.
     * Le SDK Firebase peut en valider jusqu'à 500 par appel API.
     */
    const BATCH_SIZE = 500;

    protected $messaging;

    /**
     * Create a new job instance.
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Début de la purge des tokens FCM invalides.');

        // On traite les tokens par lots pour ne pas saturer la mémoire
        FcmToken::query()->chunkById(self::BATCH_SIZE, function ($fcmTokens) {

            $tokens = $fcmTokens->pluck('token')->all();

            if (empty($tokens)) {
                return;
            }

            // Validation en masse (un seul appel API pour le lot)
            $validationResult = $this->messaging->validateTokens($tokens);

            $invalidTokens = $validationResult->invalidTokens();

            if (!empty($invalidTokens)) {
                Log::info(count($invalidTokens) . ' tokens invalides trouvés. Suppression...');
                // Suppression en une seule requête SQL
                FcmToken::whereIn('token', $invalidTokens)->delete();
            }
        });

        Log::info('Purge des tokens FCM invalides terminée.');
    }
}
