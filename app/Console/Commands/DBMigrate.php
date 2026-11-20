<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DBMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all migrations and seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->ensureDatabaseExists();

        $this->info("Dropping all tables...");
        $this->call("db:wipe");
        $this->info("Executing migrations");
        $this->call("migrate");
        $this->info("Seeding the database");
        //save.php
        $this->call("db:seed");
        // $this->fail('Ouch! That hurt! Please don\'t do that again :(');
    }

    // maakt de database aan als die nog niet bestaat, anders faalt db:wipe op een verse installatie
    private function ensureDatabaseExists(): void
    {
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");
        $database = Config::get("database.connections.{$connection}.database");

        if ($driver === 'sqlite') {
            if ($database && $database !== ':memory:' && !file_exists($database)) {
                if (!is_dir(dirname($database))) {
                    mkdir(dirname($database), 0755, true);
                }

                touch($database);
                $this->info("SQLite database aangemaakt op {$database}");
            }

            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            Config::set("database.connections.{$connection}.database", null);
            DB::purge($connection);

            $charset = Config::get("database.connections.{$connection}.charset", 'utf8mb4');
            $collation = Config::get("database.connections.{$connection}.collation", 'utf8mb4_unicode_ci');

            DB::connection($connection)->statement(
                "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET `{$charset}` COLLATE `{$collation}`"
            );

            $this->info("Database `{$database}` is klaar");

            Config::set("database.connections.{$connection}.database", $database);
            DB::purge($connection);
        }
    }
}
