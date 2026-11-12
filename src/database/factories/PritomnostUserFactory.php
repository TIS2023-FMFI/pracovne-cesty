<?php

namespace Database\Factories;

use App\Enums\PritomnostUserStatus;
use App\Models\PritomnostUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * Accounts in the stand-in of the external Pritomnost database (tests and local development only).
 */
class PritomnostUserFactory extends Factory
{
    protected $model = PritomnostUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skFake = fake('sk_SK');

        return [
            'personal_id' => (string) fake()->unique()->numberBetween(1000, 999999),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password'),
            'name' => $skFake->firstName(),
            'surname' => $skFake->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'status' => PritomnostUserStatus::REGULAR,
            'last_login' => null,
        ];
    }

    /**
     * The Pritomnost account of a person who already has an account in Cesty.
     */
    public function matching(User $user): static
    {
        return $this->state([
            'personal_id' => (string) $user->personal_id,
            'username' => $user->username,
            'password' => $user->password,
            'name' => $user->first_name,
            'surname' => $user->last_name,
            'email' => $user->email,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(['status' => PritomnostUserStatus::DISABLED]);
    }
}
