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

    protected function db(): string
    {
        return DB::connection($this->conn)->getDatabaseName();
    }

    protected function qOne(string $sql, array $params = []): object
    {
        return DB::connection($this->conn)->selectOne($sql, $params);
    }

    public function q($query, $bindings = []): array
    {
        return DB::connection($this->conn)->select($query, $bindings);
    }

    public function name(): string
    {
        return 'mysql';
    }

    public function version(): string
    {
        return $this->qOne("SELECT VERSION() AS v")->v;
    }

    public function size(): int
    {
        $row = $this->qOne("
            SELECT SUM(data_length + index_length) AS size
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$this->db()]);

        return (int)($row->size ?? 0);
    }

    public function tableCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'BASE TABLE'
        ", [$this->db()])->c;
    }

    public function viewCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'VIEW'
        ", [$this->db()])->c;
    }

    public function columnCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.columns
            WHERE table_schema = ?
        ", [$this->db()])->c;
    }

    public function indexCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(DISTINCT index_name) AS c
            FROM information_schema.statistics
            WHERE table_schema = ? AND index_name != 'PRIMARY'
        ", [$this->db()])->c;
    }

    public function primaryKeyCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND index_name = 'PRIMARY'
        ", [$this->db()])->c;
    }

    public function uniqueIndexCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(DISTINCT index_name) AS c
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND NON_UNIQUE = 0
              AND index_name != 'PRIMARY'
        ", [$this->db()])->c;
    }

    public function foreignKeyCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE table_schema = ?
              AND referenced_table_name IS NOT NULL
        ", [$this->db()])->c;
    }

    public function triggerCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.triggers
            WHERE trigger_schema = ?
        ", [$this->db()])->c;
    }

    public function procedureCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.routines
            WHERE routine_schema = ?
              AND routine_type = 'PROCEDURE'
        ", [$this->db()])->c;
    }

    public function functionCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.routines
            WHERE routine_schema = ?
              AND routine_type = 'FUNCTION'
        ", [$this->db()])->c;
    }

    public function totalRows(): int
    {
        $row = $this->qOne("
            SELECT SUM(table_rows) AS c
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$this->db()]);

        return (int)($row->c ?? 0);
    }

    public function charset(): ?string
    {
        $row = $this->qOne("
            SELECT default_character_set_name AS charset
            FROM information_schema.schemata
            WHERE schema_name = ?
        ", [$this->db()]);

        return $row->charset ?? null;
    }

    public function collation(): ?string
    {
        $row = $this->qOne("
            SELECT default_collation_name AS collation
            FROM information_schema.schemata
            WHERE schema_name = ?
        ", [$this->db()]);

        return $row->collation ?? null;
    }

    public function uptimeSeconds(): int
    {
        $row = $this->qOne("SHOW GLOBAL STATUS LIKE 'Uptime'");

        return (int)($row->Value ?? 0);
    }

    public function activeConnections(): int
    {
        $row = $this->qOne("SHOW STATUS WHERE variable_name = 'Threads_connected'");

        return (int)($row->Value ?? 0);
    }

    public function tables(): array
    {
        $rows = $this->q("
            SELECT 
                TABLE_NAME AS name,
                TABLE_ROWS AS row_count,
                ENGINE AS engine,
                TABLE_COLLATION AS collation,
                DATA_LENGTH AS data_size,
                INDEX_LENGTH AS index_size
            FROM information_schema.tables
            WHERE table_schema = ?
            ORDER BY TABLE_NAME;
        ", [$this->db()]);

        return collect($rows)->map(function ($t) {
            return [
                'name' => $t->name ?? null,
                'rows' => (int)($t->row_count ?? 0),
                'engine' => $t->engine ?? null,
                'collation' => $t->collation ?? null,
                'size' => (int)(($t->data_size ?? 0) + ($t->index_size ?? 0)),
            ];
        })->toArray();
    }
}
