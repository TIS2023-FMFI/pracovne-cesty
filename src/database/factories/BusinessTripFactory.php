<?php

namespace Database\Factories;

use App\Models\BusinessTrip;
use App\Models\ConferenceFee;
use App\Models\Country;
use App\Models\Expense;
use App\Models\Reimbursement;
use App\Models\Transport;
use App\Models\TripPurpose;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;


class BusinessTripFactory extends Factory
{
    protected $model = BusinessTrip::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
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
                bin2hex(random_bytes(16)) . '.' . fake()->fileExtension()
            ]),

            'sofia_id' => fake()->optional()->numerify('##########'),
            'state' => 0,


            'datetime_start' => $dtStart,
            'datetime_end' => $dtEnd,

            'place_start' => $placeStart,
            'place_end' => fake()->randomElement([$placeStart, fake()->city() . ', ' . fake()->country()]),

            'datetime_border_crossing_start' => $dtBorderStart,
            'datetime_border_crossing_end' => $dtBorderEnd,


            'trip_purpose_id' => TripPurpose::all()->random()->id,
            'purpose_details' => fake()->realText(50),


            'iban' => fake()->iban('SK'),

            'conference_fee_id' => fake()->randomElement([ConferenceFee::all()->random()->id, null]),
            'reimbursement_id' => fake()->randomElement([Reimbursement::all()->random()->id, null]),
            'spp_symbol_id' => Reimbursement::all()->random()->id,

            'accommodation_expense_id' => Expense::all()->random()->id,
            'travelling_expense_id' => Expense::all()->random()->id,
            'other_expense_id' => Expense::all()->random()->id,
            'allowance_id' => Expense::all()->random()->id,

            'not_reimbursed_meals' => '',
            'meals_reimbursement' => fake()->boolean(),

            'advance_amount' => fake()->randomElement([
                null,
                fake()->currencyCode() . ' ' . fake()->randomFloat(2, 0, 100)
            ]),
            'expense_estimation' => fake()->randomElement([
                null,
                fake()->currencyCode() . ' ' . fake()->randomFloat(2, 100, 1000)
            ]),

            'cancellation_reason' => '',
            'note' => fake()->optional()->realText(5000),
            'conclusion' => fake()->optional()->realText(5000),
        ];
    }
}
