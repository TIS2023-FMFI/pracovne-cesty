<?php

namespace Tests\Unit;

use App\Http\Controllers\SynchronizationController;
use App\Models\BusinessTrip;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PHPUnit\Framework\TestCase;


class SynchronizationControllerTest extends TestCase {
    public function testDateRangeCalculationSameDayEndsMorning() {
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 25, 10, 30, 0);

        $dateRange = CarbonPeriod::create($startDate->copy()->startOfDay(), '1 day', $endDate->copy()->startOfDay());

        $expectedDates = [
            '2024-12-25'
        ];

        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }
    public function testDateRangeCalculationSameDayEndsAfternoon() {
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 25, 12, 30, 0);

        $dateRange = CarbonPeriod::create($startDate->copy()->startOfDay(), '1 day', $endDate->copy()->startOfDay());

        $expectedDates = [
            '2024-12-25'
        ];

        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }


    public function testDateRangeCalculationDifferentDaysEndsMorning() {
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 30, 10, 45, 0);

        $dateRange = CarbonPeriod::create($startDate->copy()->startOfDay(), '1 day', $endDate->copy()->startOfDay());

        $expectedDates = [
            '2024-12-25',
            '2024-12-26',
            '2024-12-27',
            '2024-12-28',
            '2024-12-29',
            '2024-12-30'
        ];

        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }

    public function testDateRangeCalculationDifferentDaysBugged() {
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 30, 7, 45, 0);

        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);

        $expectedDates = [
            '2024-12-25',
            '2024-12-26',
            '2024-12-27',
            '2024-12-28',
            '2024-12-29'
        ];

        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }

    public function testDateRangeCalculationDifferentDaysFixed() {
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 30, 7, 45, 0);

        $dateRange = CarbonPeriod::create(
            $startDate->copy()->startOfDay(),
            '1 day',
            $endDate->copy()->startOfDay()
        );

        $expectedDates = [
            '2024-12-25',
            '2024-12-26',
            '2024-12-27',
            '2024-12-28',
            '2024-12-29',
            '2024-12-30'
        ];

        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }
}