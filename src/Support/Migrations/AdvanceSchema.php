<?php

namespace Basics\Support\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdvanceSchema extends Schema
{
    /**
     * Truncate a table values.
     *
     * @param  string  $table
     */
    public static function truncate(string $table)
    {
        DB::table($table)->truncate();
    }

    /**
     * Creates a table if it doesn't exists.
     *
     * @param  string  $table
     * @param  \Closure  $callback
     */
    public static function createTableIfNotExists(string $table, \Closure $callback)
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }

    /**
     * Renames a single column on a table.
     *
     * @param  string  $table
     * @param  string  $from
     * @param  string  $to
     */
    public static function renameColumn(string $table, string $from, string $to)
    {
        Schema::table($table, function (Blueprint $blueprint) use ($from, $to) {
            $blueprint->renameColumn($from, $to);
        });
    }

    /**
     * Drops a primary column and reconfigures the table
     * to use another column as primary column
     * and renames the new primary column
     * with the name of the original.
     *
     * @param  string  $table
     * @param  string  $currentKeyColumnName
     * @param  string  $newKeysColumnName
     */
    public static function replacePrimaryColumn(
        string $table,
        string $currentKeyColumnName,
        string $newKeysColumnName
    ) {
        static::throwIfColumnNotExists($table, $newKeysColumnName);

        // drop previous primary column
        if (Schema::hasColumn($table, $currentKeyColumnName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $currentKeyColumnName) {
                $blueprint->dropIndex("{$table}_{$currentKeyColumnName}_index");
                $blueprint->dropColumn($currentKeyColumnName);
            });
        }

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
     * @param  string  $table
     * @param  string  $column
     * @throws \Exception
     */
    protected static function throwIfColumnNotExists(string $table, string $column)
    {
        if (!Schema::hasColumn($table, $column)) {
            throw new \Exception("Column '{$table}'.'{$column}' doesn't exists.");
        }
    }
}
