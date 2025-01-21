<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    private const REPORT_REMINDER_TIME = '01:00';

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('invitation-links:cleanup')->hourly();
        $schedule->command('trip-reports:send-reminders')->daily()->at(self::REPORT_REMINDER_TIME);
        $schedule->command('trip-reports:send-deadline-reminders')->daily()->at(self::REPORT_REMINDER_TIME);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
