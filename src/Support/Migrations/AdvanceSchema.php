<?php

namespace Basics\Support\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
    public static function createMissingTable(string $table, \Closure $callback): void
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }

    /**
     * Adds a column to a table if it doesn't exists.
     */
    public static function addMissingColumn($table, $column, \Closure $callback): void
    {
        if (Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, $callback);
    }

    /**
     * Renames a single column on a table.
     */
    public static function renameExistingColumn(string $table, string $from, string $to): void
    {
        if (!Schema::hasColumn($table, $from)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($from, $to) {
            $blueprint->renameColumn($from, $to);
        });
    }

    /**
     * Drops an index and a column if it exists.
     */
    public static function dropExistingColumn(string $table, string $column): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            // $blueprint->dropUnique([$column]);
            // $blueprint->dropIndex([$column]);
            $blueprint->dropColumn($column);
        });
    }

    /**
     * Adds an uuid column into a table if not exists;
     * If the column already exists ensure each row has a value assigned.
     */
    public static function addMissingUuidColumn(string $table, string $column = 'uuid', string $keyColumn = 'id'): void
    {
        $isNewColumn = !Schema::hasColumn($table, $column);

        // create column as nullable
        if ($isNewColumn) {
            self::addMissingColumn($table, $column, function (Blueprint $table) use ($column, $keyColumn) {
                $table->uuid($column)->after($keyColumn)->nullable(true);
            });
        }

        // add values on the column
        foreach (DB::table($table)->where($column, null)->get() as $entry) {
            DB::table($table)
                ->where($keyColumn, $entry->{$keyColumn})
                ->update([$column => Str::uuid()->toString()]);
        }

        // change the column to non-nullable
        if ($isNewColumn) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->uuid($column)->nullable(false)->unique()->change();
            });
        }
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
        self::dropExistingColumn($table, $currentKeyColumnName);

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
