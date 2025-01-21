<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\BusinessTrip;
use App\Enums\TripState;
use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;

class TripReportDeadlineReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip-reports:deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends deadline reminders for business trip reports.';

    /**
     * Execute the console command.
     */
    public function handle() {
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
    }
}
