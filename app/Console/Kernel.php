<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Enregistrer les commandes artisan
    protected $commands = [
        Commands\BlendDBCommand::class,
    ];

    // Définir la planification des commandes
    protected function schedule(Schedule $schedule)
    {
        // Planifier l'exécution de la commande star:blend-db toutes les heures
        $schedule->command('star:blend-db')->everyThirtyMinutes();
    }

    // Enregistrer les commandes pour l'application
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}