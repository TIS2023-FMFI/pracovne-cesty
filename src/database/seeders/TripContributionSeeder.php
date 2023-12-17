<?php

namespace Database\Seeders;

use App\Models\TripContribution;
use Illuminate\Database\Seeder;

class TripContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TripContribution::factory()
            ->count(20)
            ->create();
    }
}
