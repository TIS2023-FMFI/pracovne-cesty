<?php

namespace Tests\Feature\Foundation;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DatabaseTestCase;

class UserFactoryTest extends DatabaseTestCase
{
    public static function userTypesWithRoles(): array
    {
        return [
            'employee' => ['employee', UserType::EMPLOYEE, 'traveller'],
            'PhD student' => ['phdStudent', UserType::PHD_STUDENT, 'traveller'],
            'student' => ['student', UserType::STUDENT, 'traveller'],
            'extern' => ['extern', UserType::EXTERN, 'traveller'],
            'admin' => ['admin', UserType::ADMIN, 'admin'],
        ];
    }

    #[DataProvider('userTypesWithRoles')]
    public function test_type_state_creates_an_active_user_with_the_role_of_that_type(
        string $state, UserType $expectedType, string $expectedRole
    ): void {
        $user = User::factory()->{$state}()->create()->fresh();

        $this->assertSame($expectedType, $user->user_type);
        $this->assertSame(UserStatus::ACTIVE, $user->status);
        $this->assertSame([$expectedRole], $user->getRoleNames()->all());
    }

    public function test_inactive_state_deactivates_the_user(): void
    {
        $user = User::factory()->employee()->inactive()->create()->fresh();

        $this->assertSame(UserStatus::INACTIVE, $user->status);
    }
}
