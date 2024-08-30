<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Enregistrer les commandes artisan
    protected $commands = [
        Commands\BlendDBStudientsCommand::class,
        Commands\BlendDBDegreesCommand::class,
    ];

    // Définir la planification des commandes
    protected function schedule(Schedule $schedule)
    {
        // Planifier l'exécution de la commande star:blend-db toutes les 30 minutes
        $schedule->command('star:blend-db-studients')->everyThirtyMinutes();

        // Planifier l'exécution de la commande star:blend-db-degrees toutes les 30 minutes
        $schedule->command('star:blend-db-degrees')->monthly();
    }

    // Enregistrer les commandes pour l'application
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
