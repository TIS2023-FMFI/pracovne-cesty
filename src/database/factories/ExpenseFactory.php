<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount_eur' => 'EUR ' . fake()->randomFloat(2, 0, 1000),
            'amount_foreign' => fake()->randomElement([
                null,
                fake()->currencyCode() . ' ' . fake()->randomFloat(2, 0, 1000)
            ]),
            'reimburse' => fake()->boolean()
        ];
    }
}
