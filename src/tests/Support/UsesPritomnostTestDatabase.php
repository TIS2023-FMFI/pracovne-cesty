<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Gives tests a private, disposable stand-in for the external Pritomnost database.
 *
 * The stand-in is built from the same schema file as the local development copy
 * (docker/mysql/pritomnost-schema.sql), in a separate database named in phpunit.xml.
 */
trait UsesPritomnostTestDatabase
{
    private const PRITOMNOST_CONNECTION = 'dochadzka';

    private const TEST_DATABASE_SUFFIX = '_testing';

    private const SCHEMA_FILE = 'docker/mysql/pritomnost-schema.sql';

    private static bool $pritomnostSchemaIsFresh = false;

    protected function preparePritomnostTestDatabase(): void
    {
        $this->refuseToRunAgainstRealPritomnostData();

        if (! self::$pritomnostSchemaIsFresh) {
            $this->rebuildPritomnostSchema();
            self::$pritomnostSchemaIsFresh = true;
        }

        $this->letPritomnostConnectionSeeUncommittedCestyRows();
    }

    /**
     * The schema below is dropped and re-created, so a misconfigured run must never reach
     * the development copy, let alone the production database.
     */
    private function refuseToRunAgainstRealPritomnostData(): void
    {
        $database = $this->pritomnostDatabaseName();

        if (! str_ends_with($database, self::TEST_DATABASE_SUFFIX)) {
            throw new LogicException(
                "Tests must use a Pritomnost database ending with '".self::TEST_DATABASE_SUFFIX."', "
                ."but PRITOMNOST_DB_DATABASE is '{$database}'. Check phpunit.xml."
            );
        }
    }

    private function rebuildPritomnostSchema(): void
    {
        // Created through the default connection: the Pritomnost one cannot connect before the database exists.
        DB::statement(
            "CREATE DATABASE IF NOT EXISTS `{$this->pritomnostDatabaseName()}` "
            .'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $pritomnost = DB::connection(self::PRITOMNOST_CONNECTION);
        $pritomnost->statement('DROP TABLE IF EXISTS absence, users');
        $pritomnost->unprepared(file_get_contents(base_path(self::SCHEMA_FILE)));
    }

    /**
     * User synchronisation joins the tables of both databases in one query that runs on the
     * Pritomnost connection. Rows a test has written through the Cesty connection are not
     * committed (they sit in the test's transaction), so that query would not see them
     * under the default isolation level.
     */
    private function letPritomnostConnectionSeeUncommittedCestyRows(): void
    {
        DB::connection(self::PRITOMNOST_CONNECTION)
            ->statement('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED');
    }

    private function pritomnostDatabaseName(): string
    {
        return config('database.connections.'.self::PRITOMNOST_CONNECTION.'.database');
    }
}
