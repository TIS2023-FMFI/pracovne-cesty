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
            'position_name' => 'tajomník fakulty',
            'incumbent_name' => 'Mgr. Daniel Vozár'
        ]);

        Staff::create([
            'position' => PositionTitle::DAI_CHAIR,
            'position_name' => 'vedúca KAI',
            'incumbent_name' => 'doc. RNDr. Tatiana Jajcayová, PhD.'
        ]);

        Staff::create([
            'position' => PositionTitle::FINANCIAL_DIRECTOR,
            'position_name' => 'vedúca ekonomického oddelenia',
            'incumbent_name' => 'Ing. Katarína Rusnáková'
        ]);
    }
}
