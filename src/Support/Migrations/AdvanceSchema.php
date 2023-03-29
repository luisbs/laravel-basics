<?php

namespace Basics\Support\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdvanceSchema extends Schema
{
    /**
     * Truncate a table values.
     */
    public static function truncate(string $table): void
    {
        DB::table($table)->truncate();
    }

    /**
     * Creates a table if it doesn't exists.
     */
    public static function createTableIfNotExists(string $table, \Closure $callback): void
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }

    /**
     * Renames a single column on a table.
     */
    public static function renameColumn(string $table, string $from, string $to): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($from, $to) {
            $blueprint->renameColumn($from, $to);
        });
    }

    /**
     * Drops an index and a column if it exists.
     */
    public static function dropColumnIfExists(string $table, string $column): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table, $column) {
            $blueprint->dropIndex([$column]);
            $blueprint->dropColumn($column);
        });
    }

    /**
     * Drops a primary column and reconfigures the table
     * to use another column as primary column
     * and renames the new primary column
     * with the name of the original.
     */
    public static function replacePrimaryColumn(
        string $table,
        string $currentKeyColumnName,
        string $newKeysColumnName
    ): void {
        static::throwIfColumnNotExists($table, $newKeysColumnName);

        // drop previous primary column
        self::dropColumnIfExists($table, $currentKeyColumnName);

        // set new primary column
        Schema::table($table, function (Blueprint $blueprint) use (
            $table,
            $currentKeyColumnName,
            $newKeysColumnName
        ) {
            $blueprint->dropIndex("{$table}_{$newKeysColumnName}_index");
            $blueprint->renameColumn($newKeysColumnName, $currentKeyColumnName);
            $blueprint->primary($currentKeyColumnName);
        });
    }

    /**
     * Throws an exception if a column doesn't exists in the table.
     *
     * @throws \Exception
     */
    protected static function throwIfColumnNotExists(string $table, string $column): void
    {
        if (!Schema::hasColumn($table, $column)) {
            throw new \Exception("Column '{$table}'.'{$column}' doesn't exists.");
        }
    }
}
