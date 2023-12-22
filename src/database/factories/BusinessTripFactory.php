<?php

namespace Database\Factories;

use App\Enums\TripState;
use App\Models\BusinessTrip;
use App\Models\Country;
use App\Models\SppSymbol;
use App\Models\Transport;
use App\Models\TripPurpose;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class BusinessTripFactory extends Factory
{
    protected $model = BusinessTrip::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skFake = fake('sk_SK');

        $dtStart = fake()->dateTimeBetween('-2 years');
        $dtEnd = fake()->dateTimeInInterval(
            (clone $dtStart)->modify('+3 days'),
            '+1 month'
        );

        $dtBorderStart = fake()->dateTimeInInterval($dtStart, '+2 days');
        $dtBorderEnd = fake()->dateTimeBetween($dtBorderStart, $dtEnd);


        $placeStart = $skFake->city() . ', Slovakia';

        return [
            'user_id' => User::all()->random()->id,
            'country_id' => Country::all()->random()->id,
            'transport_id' => Transport::all()->random()->id,

            'place' => fake()->city(),

            'event_url' => fake()->optional()->url(),
            'upload_name' => fake()->randomElement([
                null,
                Str::random(16) . '.' . fake()->fileExtension()
            ]),

            'sofia_id' => fake()->optional()->numerify('##########'),
            'state' => fake()->randomElement(TripState::cases()),


            'datetime_start' => $dtStart,
            'datetime_end' => $dtEnd,

            'place_start' => $placeStart,
            'place_end' => fake()->randomElement([$placeStart, fake()->city() . ', ' . fake()->country()]),

            'datetime_border_crossing_start' => $dtBorderStart,
            'datetime_border_crossing_end' => $dtBorderEnd,


            'trip_purpose_id' => TripPurpose::all()->random()->id,
            'purpose_details' => fake()->realText(50),


            'iban' => fake()->iban('SK'),

            'conference_fee_id' => null,
            'reimbursement_id' => null,
            'spp_symbol_id' => SppSymbol::all()->random()->id,

            'accommodation_expense_id' => null,
            'travelling_expense_id' => null,
            'other_expense_id' => null,
            'advance_expense_id' => null,
            'allowance_id' => null,

            'not_reimbursed_meals' => '',
            'meals_reimbursement' => fake()->boolean(),

            'expense_estimation' => fake()->randomElement([
                null,
                fake()->currencyCode() . ' ' . fake()->randomFloat(2, 100, 1000)
            ]),

            'cancellation_reason' => '',
            'note' => fake()->optional()->realText(100),
            'conclusion' => fake()->optional()->realText(100),
        ];
    }
}
