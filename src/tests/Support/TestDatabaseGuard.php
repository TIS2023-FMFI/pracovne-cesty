<?php

namespace Tests\Support;

use LogicException;

/**
 * Tests drop and re-create whole schemas. This guard makes sure they only ever do it to
 * databases that exist for that purpose, even when the environment is misconfigured
 * (e.g. DB_DATABASE exported in the shell, which would otherwise win over phpunit.xml).
 */
final class TestDatabaseGuard
{
    private const DISPOSABLE_NAME = 'testing';

    private const DISPOSABLE_SUFFIX = '_testing';

    public static function assertDisposable(string $database): void
    {
        if ($database === self::DISPOSABLE_NAME || str_ends_with($database, self::DISPOSABLE_SUFFIX)) {
            return;
        }

        throw new LogicException(
            "Refusing to run tests against database '{$database}': the name must be '"
            .self::DISPOSABLE_NAME."' or end with '".self::DISPOSABLE_SUFFIX."'. Check phpunit.xml."
        );
    }
}
