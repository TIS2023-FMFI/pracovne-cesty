<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use App\Models\BusinessTrip;
use App\Enums\TripState;
use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    private const REPORT_REMINDER_TIME = '01:00';

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('invitation-links:cleanup')->hourly();

        $schedule->call(function () {
            $completedTrips = BusinessTrip::whereDate('datetime_end', '=', Carbon::yesterday())
                ->where('state', '=', TripState::COMPLETED)
                ->get();

            foreach ($completedTrips as $trip) {
                $recipient = $trip->user->email;
                $message = 'Vaša pracovná cesta na miesto ' . $trip->place
                    . ' bola ukončená dňa ' . $trip->datetime_end
                    . '. Prosíme Vás o podanie správy z pracovnej cesty.';
            $viewTemplate = 'emails.trip_report_reminder_user';

                $email = new SimpleMail($message, $recipient, $viewTemplate, 'Pracovné cesty - upozornenie na podanie správy');

                Mail::to($recipient)->send($email);
            }
        })->daily()->at(self::REPORT_REMINDER_TIME);

        $schedule->call(function () {
            $completedTrips = BusinessTrip::whereDate('datetime_end', '=', Carbon::now()->subDays(4))
                ->where('state', '=', TripState::COMPLETED)
                ->get();

            foreach ($completedTrips as $trip) {
                $recipient = $trip->user->email;
                $message = 'Upozorňujeme Vás, že o 3 dni uplynie termín na podanie správy z pracovnej cesty ukončenej '
                . $trip->datetime_end . ' na mieste ' . $trip->place . ' .';
                $viewTemplate = 'emails.trip_report_reminder_user';

                $email = new SimpleMail($message, $recipient, $viewTemplate, 'Pracovné cesty - blížiaci sa termín na podanie správy');

                Mail::to($recipient)->send($email);
            }
        })->daily()->at(self::REPORT_REMINDER_TIME);
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
