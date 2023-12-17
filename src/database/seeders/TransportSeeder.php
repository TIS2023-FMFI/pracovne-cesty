<?php

namespace Database\Seeders;

use App\Models\Transport;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transport::insert([
            ['name' => 'služobné auto'],
            ['name' => 'vlastné auto', 'user_visible' => true],
            ['name' => 'autobus', 'user_visible' => true],
            ['name' => 'vlak', 'user_visible' => true],
            ['name' => 'lietadlo', 'user_visible' => true],
            ['name' => 'loď'],
            ['name' => 'taxi'],
            ['name' => 'bez dopravy'],
            ['name' => 'spolucestujúci'],
            ['name' => 'referentské auto']
        ]);
    }
}
