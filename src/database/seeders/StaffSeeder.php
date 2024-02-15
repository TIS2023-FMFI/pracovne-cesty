<?php

namespace Database\Seeders;

use App\Enums\PositionTitle;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // `Staff::create` creates timestamps
        Staff::create([
            'position' => PositionTitle::DEAN,
            'position_name' => 'dekan fakulty',
            'incumbent_name' => 'prof. RNDr. Daniel Ševčovič, DrSc.'
        ]);

        Staff::create([
            'position' => PositionTitle::SECRETARY,
            'position_name' => 'tajomníčka fakulty',
            'incumbent_name' => 'PaedDr. Martina Chovancová, PhD.'
        ]);
    }
}
