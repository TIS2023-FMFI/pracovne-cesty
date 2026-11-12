<?php

namespace Database\Factories;

use App\Enums\TripState;
use App\Enums\TripType;
use App\Models\BusinessTrip;
use App\Models\Country;
use App\Models\Expense;
use App\Models\SppSymbol;
use App\Models\Transport;
use App\Models\TripPurpose;
use App\Models\User;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Builds trips the way the application itself would leave them: the default is a freshly
 * submitted domestic trip, the state methods move it along its life cycle.
 * Codebooks (countries, transports, trip purposes) must be seeded.
 */
class BusinessTripFactory extends Factory
{
    private const HOME_COUNTRY = 'Slovensko';

    private const IDENTIFIER_BEFORE_CONFIRMATION = '0000';

    private const MEALS_PER_DAY = 3;

    private const MEAL_REIMBURSED = '0';

    private const EXPENSES_IN_EVERY_REPORT = ['travelling', 'accommodation', 'insurance', 'other', 'participation'];

    private const EXPENSES_IN_FOREIGN_REPORT_ONLY = ['advance'];

    protected $model = BusinessTrip::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('+10 days', '+60 days'))->setTime(8, 0);

        return [
            'user_id' => User::factory()->employee(),

            'state' => TripState::NEW,
            'sofia_id' => self::IDENTIFIER_BEFORE_CONFIRMATION,

            'type' => TripType::DOMESTIC,
            'country_id' => static fn () => Country::getIdOf(self::HOME_COUNTRY),
            'place' => fake('sk_SK')->city(),

            'transport_id' => static fn () => Transport::query()->inRandomOrder()->value('id'),
            'place_start' => 'Bratislava',
            'place_end' => 'Bratislava',
            'datetime_start' => $start,
            'datetime_end' => $start->copy()->addDays(2)->setTime(18, 0),
            'datetime_border_crossing_start' => null,
            'datetime_border_crossing_end' => null,

            'trip_purpose_id' => static fn () => TripPurpose::query()->inRandomOrder()->value('id'),
            'purpose_details' => fake()->sentence(12),
            'event_url' => null,
            'upload_name' => null,

            'iban' => fake()->iban('SK'),
            'spp_symbol_id' => SppSymbol::factory()->active(),
            'spp_symbol_id_2' => null,
            'spp_symbol_id_3' => null,
            'amount_eur' => null,
            'amount_eur_2' => null,
            'amount_eur_3' => null,

            'conference_fee_id' => null,
            'reimbursement_id' => null,

            'accommodation_expense_id' => null,
            'travelling_expense_id' => null,
            'participation_expense_id' => null,
            'insurance_expense_id' => null,
            'other_expense_id' => null,
            'advance_expense_id' => null,
            'allowance_expense_id' => null,

            'not_reimbursed_meals' => null,
            'meals_reimbursement' => true,
            'expense_estimation' => null,

            'cancellation_reason' => null,
            'note' => null,
            'conclusion' => null,

            'is_template' => false,
        ];
    }

    public function foreign(): static
    {
        return $this->state([
            'type' => TripType::FOREIGN,
            'country_id' => static fn () => Country::query()
                ->where('name', '!=', self::HOME_COUNTRY)
                ->inRandomOrder()
                ->value('id'),
            'place' => static fn () => fake()->city(),
        ]);
    }

    public function between(CarbonInterface $start, CarbonInterface $end): static
    {
        return $this->state(['datetime_start' => $start, 'datetime_end' => $end]);
    }

    /**
     * The trip in the given state, for tests that walk through all of them.
     * Like the named state methods below, it is meant to be applied once, to a trip that is still new.
     */
    public function inState(TripState $state): static
    {
        return match ($state) {
            TripState::NEW => $this->state(['state' => TripState::NEW]),
            TripState::CONFIRMED => $this->confirmed(),
            TripState::COMPLETED => $this->completed(),
            TripState::CLOSED => $this->closed(),
            TripState::CANCELLATION_REQUEST => $this->cancellationRequested(),
            TripState::CANCELLED => $this->cancelled(),
        };
    }

    /**
     * Confirmed by the secretariat, which also assigned the identifier.
     */
    public function confirmed(): static
    {
        return $this->state([
            'state' => TripState::CONFIRMED,
            'sofia_id' => static fn () => fake()->unique()->numerify('########'),
        ]);
    }

    /**
     * The traveller has returned and filed the report.
     */
    public function completed(): static
    {
        return $this->confirmed()
            ->state(['state' => TripState::COMPLETED, 'conclusion' => static fn () => fake()->sentence(12)])
            ->afterCreating(static fn (BusinessTrip $trip) => self::fileReportOf($trip));
    }

    /**
     * Accounted for and closed by the secretariat.
     */
    public function closed(): static
    {
        return $this->completed()->state(['state' => TripState::CLOSED]);
    }

    public function cancellationRequested(): static
    {
        return $this->state([
            'state' => TripState::CANCELLATION_REQUEST,
            'cancellation_reason' => static fn () => fake()->sentence(8),
        ]);
    }

    public function cancelled(): static
    {
        return $this->cancellationRequested()->state(['state' => TripState::CANCELLED]);
    }

    /**
     * Adds what a filed report leaves behind. Runs after creation, so that it sees the final
     * dates and type of the trip in whatever order the states were chained.
     */
    private static function fileReportOf(BusinessTrip $trip): void
    {
        $isForeign = $trip->type === TripType::FOREIGN;
        $expenseNames = array_merge(
            self::EXPENSES_IN_EVERY_REPORT,
            $isForeign ? self::EXPENSES_IN_FOREIGN_REPORT_ONLY : []
        );

        foreach ($expenseNames as $expenseName) {
            $expense = Expense::factory()->create([
                'amount_eur' => '25.50',
                'amount_foreign' => $isForeign ? '30.00' : null,
                'reimburse' => true,
            ]);
            $trip->setAttribute($expenseName.'_expense_id', $expense->id);
        }

        $trip->not_reimbursed_meals = str_repeat(
            self::MEAL_REIMBURSED,
            self::MEALS_PER_DAY * self::calendarDaysOf($trip)
        );

        if ($isForeign) {
            $trip->datetime_border_crossing_start = $trip->datetime_start->copy()->addHours(2);
            $trip->datetime_border_crossing_end = $trip->datetime_end->copy()->subHours(2);
        }

        $trip->save();
    }

    private static function calendarDaysOf(BusinessTrip $trip): int
    {
        return CarbonPeriod::create(
            $trip->datetime_start->copy()->startOfDay(),
            '1 day',
            $trip->datetime_end->copy()->startOfDay()
        )->count();
    }
}
