<?php

namespace App\Console\Commands;

use App\Jobs\PurgeInvalidFcmTokens;
use Illuminate\Console\Command;

/**
 * Commande de test pour lancer manuellement le job de purge des tokens FCM.
 * Utilise dispatchSync pour une exécution immédiate et un retour direct dans la console.
 */
class TestPurgeTokensJob extends Command
{
    /** @var string Nom de la commande. */
    protected $signature = 'test:purge-tokens';

    /** @var string Description de la commande. */
    protected $description = 'Lance manuellement le job PurgeInvalidFcmTokens pour le test.';

    /**
     * Exécute le job de purge en mode synchrone.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Lancement du job PurgeInvalidFcmTokens en mode synchrone...');

        // dispatchSync exécute le job immédiatement, sans passer par la file d'attente.
        // C'est parfait pour le test car les erreurs sont affichées directement.
        PurgeInvalidFcmTokens::dispatchSync();

        $this->info('Job terminé.');

        return 0;
    }
}
