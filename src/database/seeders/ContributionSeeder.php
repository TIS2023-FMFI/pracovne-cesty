<?php

namespace Database\Seeders;

use App\Models\Contribution;
use Illuminate\Database\Seeder;

class ContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contribution::insert([
            ['name' => 'vedecký výskum'],
            ['name' => 'pedagogická činnosť'],
            ['name' => 'prezentácia']
        ]);
    }
}
