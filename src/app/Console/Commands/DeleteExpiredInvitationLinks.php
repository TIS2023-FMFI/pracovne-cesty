<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteExpiredInvitationLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitation-links:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes expired or used invitation links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $deletedRows = DB::table('invitation_links')
            ->where('expires_at', '<', $now)
            ->orWhere('used', 1)
            ->delete();

        $this->info("Deleted $deletedRows expired or used invitation links.");
    }
}
