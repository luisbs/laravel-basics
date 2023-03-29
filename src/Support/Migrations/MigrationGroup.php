<?php

namespace Basics\Support\Migrations;

use Illuminate\Database\Migrations\Migration;

/**
 * Executes a group of migrations together.
 */
class MigrationGroup extends Migration
{
    /**
     * List of migrations to run.
     *
     * @var array
     */
    public static $migrations = [];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->migrations as $cls) {
            (new $cls())->up();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // runs on reverse to prevent order problems
        foreach (array_reverse($this->migrations) as $cls) {
            (new $cls())->down();
        }
    }
}
