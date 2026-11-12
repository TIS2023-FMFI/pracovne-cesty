<?php

namespace Tests;

use Database\Seeders\CodebookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\UsesPritomnostTestDatabase;

/**
 * Base class for every test that touches a database.
 *
 * The schema is migrated and the codebooks are seeded once per test run. Each test then runs
 * inside a transaction on both connections (Cesty and Pritomnost), so nothing leaks between tests.
 */
abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;
    use UsesPritomnostTestDatabase;

    /** Seed once, together with the migration, instead of in every setUp(). */
    protected $seed = true;

    protected $seeder = CodebookSeeder::class;

    protected $connectionsToTransact = ['cesty', 'dochadzka'];

    /**
     * Hook of RefreshDatabase; runs before the per-test transactions are opened.
     */
    protected function beforeRefreshingDatabase(): void
    {
        $this->preparePritomnostTestDatabase();
    }
}
