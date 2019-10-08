<?php

namespace App\Console;

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
       Commands\currentPrice::class,
       Commands\InvoiceUpdate::class,
       Commands\CloseChanUpdate::class,
       Commands\RefillLNDUpdate::class,
       Commands\TXUpdate::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:currentPrice')->everyMinute();
        $schedule->command('command:InvoiceUpdate')->everyMinute();
        $schedule->command('command:CloseChanUpdate')->everyMinute();
        $schedule->command('command:RefillLNDUpdate')->everyMinute();
        $schedule->command('command:TXUpdate')->everyFiveMinutes();
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
