<?php
/** @noinspection SqlNoDataSourceInspection */

namespace BehzadHosseinPoor\DatabaseManager\Services\Drivers;

use Illuminate\Support\Facades\DB;

class MySqlDriver implements DatabaseDriver
{
    protected string $conn;

    public function __construct(string $connection)
    {
        $this->conn = $connection;
    }

    public function name(): string
    {
        return 'mysql';
    }

    public function version(): string
    {
        return DB::connection($this->conn)->selectOne('select version() as v')->v;
    }

    public function size(): int
    {
        $db = DB::connection($this->conn)->getDatabaseName();

        $row = DB::connection($this->conn)->selectOne("
            SELECT SUM(data_length + index_length) AS size
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$db]);

        return (int)($row->size ?? 0);
    }

    public function stats(): array
    {
        $db = DB::connection($this->conn)->getDatabaseName();

        // Tables
        $tables = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'BASE TABLE'
        ", [$db]);

        // Views
        $views = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'VIEW'
        ", [$db]);

        // Columns
        $columns = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.columns
            WHERE table_schema = ?
        ", [$db]);

        // Indexes (excluding PRIMARY)
        $indexes = DB::connection($this->conn)->selectOne("
            SELECT COUNT(DISTINCT index_name) AS c
            FROM information_schema.statistics
            WHERE table_schema = ? AND index_name != 'PRIMARY'
        ", [$db]);

        // Primary Keys
        $primary = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND index_name = 'PRIMARY'
        ", [$db]);

        // Unique Indexes
        $unique = DB::connection($this->conn)->selectOne("
            SELECT COUNT(DISTINCT index_name) AS c
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND NON_UNIQUE = 0
              AND index_name != 'PRIMARY'
        ", [$db]);

        // Foreign Keys
        $fk = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE table_schema = ?
              AND referenced_table_name IS NOT NULL
        ", [$db]);

        // Triggers
        $triggers = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.triggers
            WHERE trigger_schema = ?
        ", [$db]);

        // Stored Procedures
        $procedures = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.routines
            WHERE routine_schema = ?
              AND routine_type = 'PROCEDURE'
        ", [$db]);

        // Functions
        $functions = DB::connection($this->conn)->selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.routines
            WHERE routine_schema = ?
              AND routine_type = 'FUNCTION'
        ", [$db]);


        // Total Rows in DB (SUM of table_rows)
        $totalRows = DB::connection($this->conn)->selectOne("
            SELECT SUM(table_rows) AS c
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$db]);

        // Database Charset / Collation
        $charsetInfo = DB::connection($this->conn)->selectOne("
            SELECT default_character_set_name AS charset,
                   default_collation_name AS collation
            FROM information_schema.schemata
            WHERE schema_name = ?
        ", [$db]);

        // Uptime
        $uptime = DB::connection($this->conn)->selectOne("
            SHOW GLOBAL STATUS LIKE 'Uptime'
        ");

        // Active Connections
        $activeConns = DB::connection($this->conn)->selectOne("
            SHOW STATUS WHERE variable_name = 'Threads_connected'
        ");

        return [
            'table_count' => (int)$tables->c,
            'view_count' => (int)$views->c,
            'column_count' => (int)$columns->c,
            'index_count' => (int)$indexes->c,

            'primary_keys' => (int)$primary->c,
            'unique_indexes' => (int)$unique->c,
            'foreign_keys' => (int)$fk->c,

            'triggers' => (int)$triggers->c,
            'procedures' => (int)$procedures->c,
            'functions' => (int)$functions->c,

            'total_rows' => (int)($totalRows->c ?? 0),

            'charset' => $charsetInfo->charset ?? null,
            'collation' => $charsetInfo->collation ?? null,

            'uptime_seconds' => (int)($uptime->Value ?? 0),
            'active_connections' => (int)($activeConns->Value ?? 0),
        ];
    }

    public function tables(): array
    {
        return collect(DB::connection($this->conn)
            ->select("SHOW TABLES"))
            ->map(fn($row) => array_values((array)$row)[0])
            ->toArray();
    }

    public function runQuery(string $query): array
    {
        return DB::connection($this->conn)->select($query);
    }
}
