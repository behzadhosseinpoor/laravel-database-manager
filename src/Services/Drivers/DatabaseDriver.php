<?php

namespace BehzadHosseinPoor\DatabaseManager\Services\Drivers;

interface DatabaseDriver
{
    public function database(): string;

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

    public function hasTable(string $table): bool;

    public function getColumns(string $table): array;

    public function getIndexes(string $table): array;

    public function getTableRowsCount(string $table, string $type = 'schema'): int;

    public function browse(string $table, int $page, int $perPage, ?string $orderBy = null, ?string $orderType = null): array;

    public function affectingStatement(string $query): int;

    public function select(string $query, int $page, int $perPage): array;

    public function drop(string $table, bool $foreignKeyCheck): void;

    public function truncate(string $table, bool $foreignKeyCheck): void;
}
