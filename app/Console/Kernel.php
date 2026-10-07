<?php

namespace App\Console;

use App\Jobs\PurgeInvalidFcmTokens;
use App\Models\Interaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new PurgeInvalidFcmTokens())->daily()->withoutOverlapping();
        $schedule->command('fcm:retain-latest-tokens --keep=5')->daily()->withoutOverlapping();
        $schedule->command('posts:check-obsolescence')->weeklyOn(1, '07:00')->withoutOverlapping();
        $schedule->command('model:prune', ['--model' => Interaction::class])->weeklyOn(1, '07:00')->withoutOverlapping();
        $schedule->command('posts:remind-templates')->quarterly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
