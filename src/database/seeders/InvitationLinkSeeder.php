<?php

namespace Database\Seeders;

use App\Models\InvitationLink;
use Illuminate\Database\Seeder;

class InvitationLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InvitationLink::factory()
            ->count(10)
            ->create();
    }
}
