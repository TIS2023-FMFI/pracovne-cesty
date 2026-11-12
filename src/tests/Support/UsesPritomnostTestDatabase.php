<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gives tests a private, disposable stand-in for the external Pritomnost database.
 *
 * The stand-in is built from the same schema file as the local development copy
 * (docker/mysql/pritomnost-schema.sql), in a separate database named in phpunit.xml.
 *
 * Assumes what the application itself assumes: both databases live on one MySQL server
 * (user synchronisation joins them in a single query) and the Cesty user may create the stand-in.
 */
trait UsesPritomnostTestDatabase
{
    private const PRITOMNOST_CONNECTION = 'dochadzka';

    private const SCHEMA_FILE = 'docker/mysql/pritomnost-schema.sql';

    private static bool $pritomnostSchemaIsFresh = false;

    protected function preparePritomnostTestDatabase(): void
    {
        TestDatabaseGuard::assertDisposable($this->pritomnostDatabaseName());

        if (! self::$pritomnostSchemaIsFresh) {
            $this->rebuildPritomnostSchema();
            self::$pritomnostSchemaIsFresh = true;
        }

        $this->letPritomnostConnectionSeeUncommittedCestyRows();
    }

    private function rebuildPritomnostSchema(): void
    {
        // Created through the default connection: the Pritomnost one cannot connect before the database exists.
        DB::statement(
            "CREATE DATABASE IF NOT EXISTS `{$this->pritomnostDatabaseName()}` "
            .'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        Schema::connection(self::PRITOMNOST_CONNECTION)->dropAllTables();
        DB::connection(self::PRITOMNOST_CONNECTION)->unprepared(file_get_contents(base_path(self::SCHEMA_FILE)));
    }

    /**
     * User synchronisation joins the tables of both databases in one query that runs on the
     * Pritomnost connection. Rows a test has written through the Cesty connection are not
     * committed (they sit in the test's transaction), so that query would not see them
     * under the default isolation level.
     *
     * The setting belongs to the current session: a test that reconnects to Pritomnost loses it.
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
