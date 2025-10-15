<?php

namespace App\Console\Commands;

use App\Jobs\PurgeInvalidFcmTokens;
use Illuminate\Console\Command;

class TestPurgeTokensJob extends Command
{
    // Nom de la commande que vous taperez dans le terminal
    protected $signature = 'test:purge-tokens';

    protected $description = 'Lance manuellement le job PurgeInvalidFcmTokens pour le test.';

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
