<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Models\PritomnostUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\DatabaseTestCase;

class LoginTest extends DatabaseTestCase
{
    public function test_user_without_pritomnost_account_logs_in_with_local_password(): void
    {
        $user = User::factory()->extern()->create(['password' => Hash::make('local-secret')]);

        $this->post(route('user.login'), ['username' => $user->username, 'password' => 'local-secret']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->extern()->create(['password' => Hash::make('local-secret')]);

        $this->post(route('user.login'), ['username' => $user->username, 'password' => 'wrong']);

        $this->assertGuest();
    }

    public function test_first_login_of_a_pritomnost_user_creates_an_employee_account(): void
    {
        $pritomnostUser = PritomnostUser::factory()->create(['password' => Hash::make('shared-secret')]);

        $this->post(route('user.login'), ['username' => $pritomnostUser->username, 'password' => 'shared-secret']);

        $user = User::where('personal_id', $pritomnostUser->personal_id)->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(UserType::EMPLOYEE, $user->user_type);
        $this->assertTrue($user->hasRole('traveller'));
    }

    public function test_login_of_a_known_pritomnost_user_takes_over_the_password_from_pritomnost(): void
    {
        $user = User::factory()->employee()->create(['password' => Hash::make('stale-secret')]);
        PritomnostUser::factory()->matching($user)->create(['password' => Hash::make('changed-in-pritomnost')]);

        $this->post(route('user.login'), ['username' => $user->username, 'password' => 'changed-in-pritomnost']);

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::where('personal_id', $user->personal_id)->count());
    }
}
