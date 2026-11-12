<?php

namespace Tests\Feature\Foundation;

use App\Enums\TripState;
use App\Enums\TripType;
use App\Models\BusinessTrip;
use App\Models\Country;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DatabaseTestCase;

class BusinessTripFactoryTest extends DatabaseTestCase
{
    public static function tripStates(): array
    {
        return collect(TripState::cases())
            ->mapWithKeys(static fn (TripState $state) => [$state->name => [$state]])
            ->all();
    }

    public function test_default_trip_is_a_new_domestic_trip_of_an_employee(): void
    {
        $trip = BusinessTrip::factory()->create()->fresh();

        $this->assertSame(TripState::NEW, $trip->state);
        $this->assertSame(TripType::DOMESTIC, $trip->type);
        $this->assertSame(Country::getIdOf('Slovensko'), $trip->country_id);
        $this->assertTrue($trip->user->hasRole('traveller'));
        $this->assertNotNull($trip->sppSymbol);
        $this->assertTrue($trip->datetime_end->isAfter($trip->datetime_start));
    }

    public function test_foreign_state_picks_a_country_other_than_slovakia(): void
    {
        $trip = BusinessTrip::factory()->foreign()->create()->fresh();

        $this->assertSame(TripType::FOREIGN, $trip->type);
        $this->assertNotSame(Country::getIdOf('Slovensko'), $trip->country_id);
    }

    #[DataProvider('tripStates')]
    public function test_owner_and_secretariat_can_open_a_trip_in_every_state(TripState $state): void
    {
        $owner = User::factory()->employee()->create();
        $secretariat = User::factory()->admin()->create();

        $trip = BusinessTrip::factory()->for($owner)->inState($state)->create();

        $this->assertSame($state, $trip->fresh()->state);
        $this->actingAs($owner)->get(route('trip.edit', $trip))->assertOk();
        $this->actingAs($secretariat)->get(route('trip.edit', $trip))->assertOk();
    }

    #[DataProvider('tripStates')]
    public function test_foreign_trip_can_be_opened_in_every_state(TripState $state): void
    {
        $trip = BusinessTrip::factory()->foreign()->inState($state)->create();

        $this->actingAs($trip->user)->get(route('trip.edit', $trip))->assertOk();
    }

    public function test_only_confirmed_and_later_trips_carry_an_identifier(): void
    {
        $this->assertSame('0000', BusinessTrip::factory()->create()->fresh()->sofia_id);
        $this->assertNotSame('0000', BusinessTrip::factory()->confirmed()->create()->fresh()->sofia_id);
    }

    public function test_completed_trip_carries_a_full_report_whatever_the_order_of_states(): void
    {
        $start = now()->subDays(10)->setTime(8, 0);

        $trip = BusinessTrip::factory()
            ->completed()
            ->foreign()
            ->between($start, $start->copy()->addDays(4)->setTime(18, 0))
            ->create()
            ->fresh();

        $this->assertNotNull($trip->conclusion);
        $this->assertSame(5 * 3, strlen($trip->not_reimbursed_meals));
        foreach (['travelling', 'accommodation', 'insurance', 'other', 'participation', 'advance'] as $expense) {
            $this->assertNotNull($trip->{$expense.'Expense'}, "missing {$expense} expense");
        }
    }

    public function test_domestic_completed_trip_has_no_advance_expense(): void
    {
        $trip = BusinessTrip::factory()->completed()->create()->fresh();

        $this->assertNull($trip->advanceExpense);
        $this->assertSame(3 * 3, strlen($trip->not_reimbursed_meals));
    }

    public function test_cancellation_states_carry_a_reason(): void
    {
        $this->assertNotEmpty(BusinessTrip::factory()->cancellationRequested()->create()->cancellation_reason);
        $this->assertNotEmpty(BusinessTrip::factory()->cancelled()->create()->cancellation_reason);
    }
}
