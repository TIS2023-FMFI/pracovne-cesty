<?php

namespace Tests\Feature\Foundation;

use App\Models\Contribution;
use App\Models\Country;
use App\Models\Transport;
use App\Models\TripPurpose;
use Spatie\Permission\Models\Role;
use Tests\DatabaseTestCase;

class CodebooksTest extends DatabaseTestCase
{
    public function test_codebooks_are_seeded_for_every_test(): void
    {
        $this->assertNotNull(Country::where('name', 'Slovensko')->first());
        $this->assertGreaterThan(1, Country::count());
        $this->assertGreaterThan(0, Transport::count());
        $this->assertGreaterThan(0, TripPurpose::count());
        $this->assertGreaterThan(0, Contribution::count());
    }

    public function test_access_roles_are_seeded_for_every_test(): void
    {
        $this->assertEqualsCanonicalizing(['admin', 'traveller'], Role::pluck('name')->all());
    }
}
