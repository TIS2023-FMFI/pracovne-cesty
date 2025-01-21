<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\BusinessTrip;
use App\Enums\TripState;
use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;

class TripReportReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip-reports:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminders for business trip reports.';

    /**
     * Execute the console command.
     */
    public function handle() {
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
    }
}
