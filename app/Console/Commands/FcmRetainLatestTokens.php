<?php

namespace App\Console\Commands;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Console\Command;

class FcmRetainLatestTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:retain-latest-tokens {--keep=5 : Le nombre de tokens les plus récents à conserver par utilisateur.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pour les utilisateurs avec trop de tokens FCM, ne conserve que les plus récents.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tokensToKeep = (int) $this->option('keep');
        $this->info("Début du nettoyage : conservation des {$tokensToKeep} tokens les plus récents par utilisateur.");

        // Étape 1 : Trouver les utilisateurs qui ont PLUS de N tokens en utilisant Eloquent
        $usersToClean = User::withCount('fcmTokens')
            ->having('fcm_tokens_count', '>', $tokensToKeep)
            ->get();

        if ($usersToClean->isEmpty()) {
            $this->info('Aucun utilisateur ne nécessite de nettoyage. Opération terminée.');
            return 0;
        }

        $this->info($usersToClean->count() . ' utilisateur(s) à traiter.');
        $totalDetached = 0;

        $bar = $this->output->createProgressBar($usersToClean->count());
        $bar->start();

        foreach ($usersToClean as $user) {
            // Étape 2 : Pour chaque utilisateur, trouver les IDs des N tokens les plus récents à CONSERVER
            $tokenIdsToKeep = $user->fcmTokens()
                ->latest('fcm_tokens.created_at') // On ordonne par la date de création du token
                ->limit($tokensToKeep)
                ->pluck('fcm_tokens.id'); // On récupère les IDs des tokens

            // Étape 3 : Détacher tous les autres tokens pour cet utilisateur
            $tokenIdsToDetach = $user->fcmTokens()
                ->whereNotIn('fcm_tokens.id', $tokenIdsToKeep)
                ->pluck('fcm_tokens.id');

            if ($tokenIdsToDetach->isNotEmpty()) {
                $user->fcmTokens()->detach($tokenIdsToDetach);
                $totalDetached += $tokenIdsToDetach->count();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Opération de détachement terminée. {$totalDetached} relation(s) token-utilisateur redondante(s) ont été supprimées.");

        // Étape 4 : Nettoyage final des tokens orphelins (ceux qui ne sont plus liés à aucun utilisateur)
        $this->info('Recherche des tokens orphelins à supprimer...');

        $orphanedTokensQuery = FcmToken::whereDoesntHave('users');
        $orphanedCount = $orphanedTokensQuery->count();

        if ($orphanedCount > 0) {
            $deletedCount = $orphanedTokensQuery->delete();
            $this->info("{$deletedCount} token(s) orphelin(s) ont été supprimés.");
        } else {
            $this->info('Aucun token orphelin trouvé.');
        }

        return 0;
    }
}
