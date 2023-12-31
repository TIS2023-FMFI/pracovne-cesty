<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class TripContributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_trip_id' => null,
            'contribution_id' => null,
            'detail' => fake('sk_SK')->optional()->realText(100)
        ];
    }
}
