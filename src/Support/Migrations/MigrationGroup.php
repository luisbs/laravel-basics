<?php

namespace Basics\Support\Migrations;

use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

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
    protected $migrations = [];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $output = new ConsoleOutput();
        foreach ($this->migrations as $cls) {
            $output->writeln("Running {$cls}->up()");
            (new $cls())->up();
            $output->writeln("Done {$cls}->up()");
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
        $output = new ConsoleOutput();
        foreach (array_reverse($this->migrations) as $cls) {
            $output->writeln("Running {$cls}->down()");
            (new $cls())->down();
            $output->writeln("Done {$cls}->down()");
        }
    }
}
