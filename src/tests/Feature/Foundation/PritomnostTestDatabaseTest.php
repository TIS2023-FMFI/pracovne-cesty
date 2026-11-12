<?php

namespace Tests\Feature\Foundation;

use App\Enums\PritomnostUserStatus;
use App\Models\PritomnostAbsence;
use App\Models\PritomnostUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\DatabaseTestCase;

class PritomnostTestDatabaseTest extends DatabaseTestCase
{
    public function test_tests_never_touch_the_development_copy_of_pritomnost(): void
    {
        $this->assertSame('dochadzka_testing', DB::connection('dochadzka')->getDatabaseName());
    }

    public function test_misconfigured_run_is_stopped_before_it_reaches_real_pritomnost_data(): void
    {
        config(['database.connections.dochadzka.database' => 'dochadzka']);

        $this->expectException(LogicException::class);

        $this->preparePritomnostTestDatabase();
    }

    public function test_every_test_starts_with_empty_pritomnost_tables(): void
    {
        $this->assertSame(0, PritomnostUser::count());
        $this->assertSame(0, PritomnostAbsence::count());

        PritomnostUser::factory()->create();
        $this->assertSame(1, PritomnostUser::count());
    }

    public function test_rows_of_the_previous_test_are_rolled_back(): void
    {
        $this->assertSame(0, PritomnostUser::count());

        PritomnostUser::factory()->create();
        $this->assertSame(1, PritomnostUser::count());
    }

    public function test_factory_creates_a_regular_pritomnost_user(): void
    {
        $pritomnostUser = PritomnostUser::factory()->create()->fresh();

        $this->assertSame(PritomnostUserStatus::REGULAR, $pritomnostUser->status);
        $this->assertNotEmpty($pritomnostUser->username);
    }

    public function test_matching_state_links_the_pritomnost_user_to_a_cesty_user(): void
    {
        $user = User::factory()->employee()->create();

        $pritomnostUser = PritomnostUser::factory()->matching($user)->create();

        $this->assertSame($user->personal_id, $pritomnostUser->personal_id);
        $this->assertTrue($user->pritomnostUser->is($pritomnostUser));
    }
}
