<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skFake = fake('sk_SK');

        $gender = fake()->randomElement(['female', 'male']);

        $titles = [];
        for ($i = 0; $i < 3; $i++) {
            $title = $skFake->optional()->title();

            if (!is_null($title)) {
                $titles[] = $title;
            }
        }

        $titles = implode(', ', $titles);

        return [
            'first_name' => $skFake->firstName($gender),
            'last_name' => $skFake->lastName($gender),
            'academic_degrees' => $titles,
            'personal_id' => fake()->randomNumber(8, true),
            'department' => fake()->randomElement(['KAI', 'KDMFI', 'KI', 'KAMŠ', 'KAG', 'KMANM']),
            'email' => $skFake->email(),
            'address' => $skFake->address(),

            'user_type' => fake()->randomElement(UserType::cases()),
            'username' => fake()->userName(),
            'password' => Hash::make('password'),

            'status' => fake()->randomElement(UserStatus::cases()),
            'last_login' => fake()->dateTimeThisDecade(),

            'iban' => fake()->iban('SK'),
            'spp_user_type' => fake()->numberBetween(1, 10)
        ];
    }
}
