<?php

namespace Database\Factories;

use App\Models\BusinessTrip;
use App\Models\Contribution;
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
            'business_trip_id' => BusinessTrip::all()->random()->id,
            'contribution_id' => Contribution::all()->random()->id,
            'detail' => fake('sk_SK')->optional()->realText(200)
        ];
    }
}
