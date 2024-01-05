<?php

namespace Database\Seeders;

use App\Models\SppSymbol;
use Illuminate\Database\Seeder;

class SppSymbolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SppSymbol::factory()
            ->count(10)
            ->create();
    }
}
