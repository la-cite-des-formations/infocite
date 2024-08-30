<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Star\StarDBController;

class BlendDBDegreesCommand extends Command
{
    // Le nom et la signature de la commande console.
    protected $signature = 'star:blend-db-degrees';

    // La description de la commande console.
    protected $description = 'Synchronize degrees with the external API';

    // Exécuter la commande console.
    public function handle()
    {
        StarDBController::BlendDBDegrees();
        $this->info('Degree synchronization completed successfully.');
    }
}