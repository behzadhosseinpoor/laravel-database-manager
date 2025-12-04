<?php

namespace BehzadHosseinPoor\DatabaseManager\Services\Drivers;

interface DatabaseDriver
{
    public function name(): string;

    public function version(): string;

    public function size(): int;

    public function tableCount(): int;

    public function viewCount(): int;

    public function columnCount(): int;

    public function indexCount(): int;

    public function primaryKeyCount(): int;

    public function uniqueIndexCount(): int;

    public function foreignKeyCount(): int;

    public function triggerCount(): int;

    public function procedureCount(): int;

    public function functionCount(): int;

    public function totalRows(): int;

    public function charset(): ?string;

    public function collation(): ?string;

    public function uptimeSeconds(): int;

    public function activeConnections(): int;

    public function tables(): array;
}
