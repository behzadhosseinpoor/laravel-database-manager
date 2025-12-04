<?php

namespace BehzadHosseinPoor\DatabaseManager\Services\Drivers;

interface DatabaseDriver
{
    public function name(): string;

    public function version(): string;

    public function size(): int;

    public function stats(): array;

    public function tables(): array;

    public function runQuery(string $query): array;
}
