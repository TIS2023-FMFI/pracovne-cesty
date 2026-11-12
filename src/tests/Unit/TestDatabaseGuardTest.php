<?php

namespace Tests\Unit;

use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabaseGuard;

class TestDatabaseGuardTest extends TestCase
{
    public static function disposableNames(): array
    {
        return [['testing'], ['dochadzka_testing'], ['cesty_testing']];
    }

    public static function realNames(): array
    {
        return [['cesty'], ['dochadzka'], ['testing_backup'], ['']];
    }

    #[DataProvider('disposableNames')]
    public function test_databases_meant_for_tests_are_accepted(string $database): void
    {
        TestDatabaseGuard::assertDisposable($database);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('realNames')]
    public function test_any_other_database_stops_the_run(string $database): void
    {
        $this->expectException(LogicException::class);

        TestDatabaseGuard::assertDisposable($database);
    }
}
