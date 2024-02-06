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
        // Run in the hierarchical order
        $this->call([
            // Factory-less
            CountrySeeder::class,
            TransportSeeder::class,
            ContributionSeeder::class,
            TripPurposeSeeder::class,
            StaffSeeder::class,
            RoleAndPermissionSeeder::class,

            // With factories
            // Without foreign references
            InvitationLinkSeeder::class,
            SppSymbolSeeder::class,
            ConferenceFeeSeeder::class,
            ExpenseSeeder::class,
            UserSeeder::class,

            // With foreign references
            ReimbursementSeeder::class,
            BusinessTripSeeder::class,
            TripContributionSeeder::class
        ]);
    }
}
