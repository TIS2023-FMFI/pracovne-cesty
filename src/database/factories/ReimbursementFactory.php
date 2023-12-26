<?php

namespace Database\Factories;

use App\Models\SppSymbol;
use Illuminate\Database\Eloquent\Factories\Factory;


class ReimbursementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'spp_symbol_id' => SppSymbol::all()->random()->id,
            'reimbursement_date' => fake()
                ->dateTimeBetween('-2 years', '+2 years')
                ->format('Y-m-d')
        ];
    }
}
