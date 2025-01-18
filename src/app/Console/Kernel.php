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
        })->daily()->at('20:02');


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
