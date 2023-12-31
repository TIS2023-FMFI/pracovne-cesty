<?php

namespace Database\Seeders;

use App\Models\BusinessTrip;
use App\Models\TripContribution;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TripContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = BusinessTrip::crossJoin('contributions')
            ->select(['business_trips.id AS bid', 'contributions.id AS cid'])
            ->get()
            ->shuffle();

        TripContribution::factory()
            ->count(20)
            ->sequence(fn(Sequence $sequence) => [
                'business_trip_id' => $product[$sequence->index]->bid,
                'contribution_id' => $product[$sequence->index]->cid
            ])
            ->create();
    }
}
