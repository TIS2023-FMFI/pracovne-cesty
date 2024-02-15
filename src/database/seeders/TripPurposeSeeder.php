<?php

namespace Database\Seeders;

use App\Models\TripPurpose;
use Illuminate\Database\Seeder;

class TripPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TripPurpose::insert([
            ['name' => 'konferencia'],
            ['name' => 'pracovné stretnutie'],
            ['name' => 'študijný pobyt']
        ]);
    }
}
