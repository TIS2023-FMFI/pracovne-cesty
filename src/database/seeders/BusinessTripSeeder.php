<?php

namespace Database\Seeders;

use App\Models\BusinessTrip;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class BusinessTripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessTrip::factory()
            ->count(20)
            ->sequence(fn(Sequence $sequence) => [
                'conference_fee_id' => $sequence->index + 1,
                'reimbursement_id' => $sequence->index + 1,

                'accommodation_expense_id' => 5 * $sequence->index + 1,
                'travelling_expense_id' => 5 * $sequence->index + 2,
                'participation_expense_id' => 5 * $sequence->index + 3,
                'insurance_expense_id' => 5 * $sequence->index + 4,
                'other_expense_id' => 5 * $sequence->index + 5,
                'advance_expense_id' => 5 * $sequence->index + 6,
                'allowance_expense_id' => 5 * $sequence->index + 7,
            ])
            ->create();
    }
}
