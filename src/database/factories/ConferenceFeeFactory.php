<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConferenceFee>
 */
class ConferenceFeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organiser_name' => fake()->company(),
            'organiser_address' => fake()->address(),
            'ico' => fake('cs_CZ')->optional()->ico(),
            'iban' => fake()->iban(),
            'amount' => fake()->currencyCode() . ' ' . fake()->randomFloat(2, 25, 1250)
        ];
    }
}
