<?php

namespace Database\Factories;

use App\Enums\SppStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


class SppSymbolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'spp_symbol' => fake()->randomElement([
                fake()->regexify('[A-Z]-\d{6}'),
                fake()->regexify('[A-Z0-9]{7}')
            ]),
            'functional_region' => fake()->numerify('0####'),
            'financial_centre' => '107240',
            'grantee' => User::factory(),
            'status' => fake()->randomElement(SppStatus::cases()),
            'agency' => fake()->company(),
            'acronym' => fake()->regexify('[A-Z]-\d{4}')
        ];
    }
}
