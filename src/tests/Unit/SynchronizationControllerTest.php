<?php

namespace Tests\Unit;

use App\Http\Controllers\SynchronizationController;
use App\Models\BusinessTrip;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PHPUnit\Framework\TestCase;


class SynchronizationControllerTest extends TestCase {
    public function testDateRangeCalculationSameDayEndsMorning() {
        // Create a mock BusinessTrip object
        $businessTrip = $this->createMock(BusinessTrip::class);

        // Define start and end dates
        // 25.12 8:45 do 25.12 12:30
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 25, 10, 30, 0);

        // Set the datetime_start and datetime_end properties of the mock object
        $businessTrip->datetime_start = $startDate;
        $businessTrip->datetime_end = $endDate;

        // Calculate the date range
        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);

        // Expected dates in the range
        $expectedDates = [
            '2024-12-25'
        ];

        // Assert that the calculated date range matches the expected dates
        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }
    public function testDateRangeCalculationSameDayEndsAfternoon() {
        // Create a mock BusinessTrip object
        $businessTrip = $this->createMock(BusinessTrip::class);

        // Define start and end dates
        // 25.12 8:45 do 25.12 12:30
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 25, 12, 30, 0);

        // Set the datetime_start and datetime_end properties of the mock object
        $businessTrip->datetime_start = $startDate;
        $businessTrip->datetime_end = $endDate;

        // Calculate the date range
        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);

        // Expected dates in the range
        $expectedDates = [
            '2024-12-25'
        ];

        // Assert that the calculated date range matches the expected dates
        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }


    public function testDateRangeCalculationDifferentDaysEndsMorning() {
        // Create a mock BusinessTrip object
        $businessTrip = $this->createMock(BusinessTrip::class);

        // Define start and end dates
        // 25.12 8:45 do 25.12 12:30
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 30, 10, 45, 0);

        // Set the datetime_start and datetime_end properties of the mock object
        $businessTrip->datetime_start = $startDate;
        $businessTrip->datetime_end = $endDate;

        // Calculate the date range
        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);

        // Expected dates in the range
        $expectedDates = [
            '2024-12-25',
            '2024-12-26',
            '2024-12-27',
            '2024-12-28',
            '2024-12-29',
            '2024-12-30'
        ];

        // Assert that the calculated date range matches the expected dates
        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }

    public function testDateRangeCalculationDifferentDaysEndsAfternoon() {
        // Create a mock BusinessTrip object
        $businessTrip = $this->createMock(BusinessTrip::class);

        // Define start and end dates
        // 25.12 8:45 do 25.12 12:30
        $startDate = Carbon::create(2024, 12, 25, 8, 45, 0);
        $endDate = Carbon::create(2024, 12, 30, 12, 45, 0);

        // Set the datetime_start and datetime_end properties of the mock object
        $businessTrip->datetime_start = $startDate;
        $businessTrip->datetime_end = $endDate;

        // Calculate the date range
        $dateRange = CarbonPeriod::create($startDate, '1 day', $endDate);

        // Expected dates in the range
        $expectedDates = [
            '2024-12-25',
            '2024-12-26',
            '2024-12-27',
            '2024-12-28',
            '2024-12-29',
            '2024-12-30'
        ];

        // Assert that the calculated date range matches the expected dates
        $this->assertEquals($expectedDates, array_map(function ($date) {
            return $date->format('Y-m-d');
        }, iterator_to_array($dateRange)));
    }
}