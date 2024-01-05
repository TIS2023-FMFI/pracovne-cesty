<?php

namespace Database\Seeders;

use App\Models\Reimbursement;
use Illuminate\Database\Seeder;

class ReimbursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reimbursement::factory()
            ->count(20)
            ->create();
    }
}
