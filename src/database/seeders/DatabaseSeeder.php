<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BusinessTripSeeder::class,
            ConferenceFeeSeeder::class,
            ContributionSeeder::class,
            CountrySeeder::class,
            ExpenseSeeder::class,
            ReimbursementSeeder::class,
            SppSymbolSeeder::class,
            StaffSeeder::class,
            TransportSeeder::class,
            TripContributionSeeder::class,
            TripPurposeSeeder::class,
            UserSeeder::class
        ]);
    }
}
