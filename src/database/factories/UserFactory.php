<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{
    /**
     * Personal numbers above this one are placeholders the application hands out to people
     * without a real one; user synchronisation with Pritomnost skips them.
     */
    private const HIGHEST_REAL_PERSONAL_ID = 10790001;

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
            'personal_id' => (string) fake()->unique()->numberBetween(1000, self::HIGHEST_REAL_PERSONAL_ID),
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

    public function employee(): static
    {
        return $this->activeOfType(UserType::EMPLOYEE);
    }

    public function phdStudent(): static
    {
        return $this->activeOfType(UserType::PHD_STUDENT);
    }

    public function student(): static
    {
        return $this->activeOfType(UserType::STUDENT);
    }

    public function extern(): static
    {
        return $this->activeOfType(UserType::EXTERN);
    }

    /**
     * The secretariat's account (the application calls it "admin").
     */
    public function admin(): static
    {
        return $this->activeOfType(UserType::ADMIN);
    }

    public function inactive(): static
    {
        return $this->state(['status' => UserStatus::INACTIVE]);
    }

    /**
     * An active user of the given type, holding the access role that belongs to it.
     * The role must exist (RoleAndPermissionSeeder).
     */
    private function activeOfType(UserType $type): static
    {
        return $this
            ->state(['user_type' => $type, 'status' => UserStatus::ACTIVE])
            ->afterCreating(static fn (User $user) => $user->assignRole($type->role()));
    }
}
