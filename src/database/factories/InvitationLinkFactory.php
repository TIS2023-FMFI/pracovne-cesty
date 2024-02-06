<?php

namespace Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'email' => fake()->email(),
            'token' => bin2hex(random_bytes(20)),
            'expires_at' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'used' => fake()->boolean()
        ];
    }
}
