<?php

namespace App\Console\Commands;

use App\Http\Controllers\Star\StarDBController;
use Illuminate\Console\Command;

class BlendDBStudientsCommand extends Command
{
    // Le nom et la signature de la commande console
    protected $signature = 'star:blend-db-studients';

    // La description de la commande console
    protected $description = 'Blend the Star DB periodically';

    // Exécuter la commande console
    public function handle()
    {
        // Appeler la méthode BlendDB de votre contrôleur
        StarDBController::BlendDBStudients();

        // Afficher un message de succès
        $this->info('BlendDB executed successfully.');
    }
}
