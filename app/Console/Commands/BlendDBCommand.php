<?php

namespace App\Console\Commands;

use App\Http\Controllers\Star\StarDBController;
use Illuminate\Console\Command;

class BlendDBCommand extends Command
{
    // Le nom et la signature de la commande console
    protected $signature = 'star:blend-db';

    // La description de la commande console
    protected $description = 'Blend the Star DB periodically';

    // Exécuter la commande console
    public function handle()
    {
        // Appeler la méthode BlendDB de votre contrôleur
        StarDBController::BlendDB();

        // Afficher un message de succès
        $this->info('BlendDB executed successfully.');
    }
}
