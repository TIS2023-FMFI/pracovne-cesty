<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Reference data the application cannot work without: codebooks, signatories and access roles.
 * Depends on no other data, so it is also what the automated tests start from.
 */
class CodebookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            TransportSeeder::class,
            ContributionSeeder::class,
            TripPurposeSeeder::class,
            StaffSeeder::class,
            RoleAndPermissionSeeder::class,
        ]);
    }
}
