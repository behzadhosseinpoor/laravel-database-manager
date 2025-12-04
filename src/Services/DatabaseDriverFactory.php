<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Services;

use BehzadHosseinPoor\DatabaseManager\Services\Drivers\DatabaseDriver;
use BehzadHosseinPoor\DatabaseManager\Services\Drivers\MySqlDriver;
use Exception;

class DatabaseDriverFactory
{
    public static function make(string $connection): DatabaseDriver
    {
        $config = config("database.connections.$connection");

        $driver = $config['driver'] ?? null;

        return match ($driver) {
            'mysql' => new MySqlDriver($connection),

            default => throw new Exception("Unsupported database driver: $driver"),
        };
    }
}
