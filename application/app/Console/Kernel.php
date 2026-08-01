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
        //
        Commands\ProcessAPI::class,
        Commands\ProcessDaily::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('act:processapi')->everyMinute();

        // TEMP testing: ROI every 10 minutes (uses advancing fake dates).
        // After testing: set income.test_roi.enabled = false and keep only dailyAt('00:05').
        if (config('income.test_roi.enabled', false)) {
            $minutes = max(1, (int) config('income.test_roi.interval_minutes', 10));
            $schedule->command('act:processdaily --test')->cron('*/'.$minutes.' * * * *');
        } else {
            $schedule->command('act:processdaily')->dailyAt('00:05');
        }
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
