<?php

namespace Database\Seeders;

use App\Models\ConferenceFee;
use Illuminate\Database\Seeder;

class ConferenceFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConferenceFee::factory()
            ->count(20)
            ->create();
    }
}
