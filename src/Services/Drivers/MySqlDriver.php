<?php
/** @noinspection PhpIncompatibleReturnTypeInspection */

/** @noinspection SqlNoDataSourceInspection */

namespace BehzadHosseinPoor\DatabaseManager\Services\Drivers;

use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MySqlDriver implements DatabaseDriver
{
    protected string $conn;

    public function __construct(string $connection)
    {
        $this->conn = $connection;
    }

    protected function db(): MySqlConnection
    {
        return DB::connection($this->conn);
    }

    protected function schema(): MySqlBuilder
    {
        return Schema::connection($this->conn);
    }

    protected function qOne(string $sql, array $params = []): object
    {
        return DB::connection($this->conn)->selectOne($sql, $params);
    }

    public function q($query, $bindings = []): array
    {
        return DB::connection($this->conn)->select($query, $bindings);
    }

    public function database(): string
    {
        return $this->db()->getDatabaseName();
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
        ", [$this->database()]);

        return (int)($row->size ?? 0);
    }

    public function tableCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'BASE TABLE'
        ", [$this->database()])->c;
    }

    public function viewCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'VIEW'
        ", [$this->database()])->c;
    }

    public function columnCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.columns
            WHERE table_schema = ?
        ", [$this->database()])->c;
    }

    public function indexCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(DISTINCT index_name) AS c
            FROM information_schema.statistics
            WHERE table_schema = ? AND index_name != 'PRIMARY'
        ", [$this->database()])->c;
    }

    public function primaryKeyCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND index_name = 'PRIMARY'
        ", [$this->database()])->c;
    }

    public function uniqueIndexCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(DISTINCT index_name) AS c
            FROM information_schema.statistics
            WHERE table_schema = ?
              AND NON_UNIQUE = 0
              AND index_name != 'PRIMARY'
        ", [$this->database()])->c;
    }

    public function foreignKeyCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE table_schema = ?
              AND referenced_table_name IS NOT NULL
        ", [$this->database()])->c;
    }

    public function triggerCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.triggers
            WHERE trigger_schema = ?
        ", [$this->database()])->c;
    }

    public function procedureCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.routines
            WHERE routine_schema = ?
              AND routine_type = 'PROCEDURE'
        ", [$this->database()])->c;
    }

    public function functionCount(): int
    {
        return (int)$this->qOne("
            SELECT COUNT(*) AS c
            FROM information_schema.routines
            WHERE routine_schema = ?
              AND routine_type = 'FUNCTION'
        ", [$this->database()])->c;
    }

    public function totalRows(): int
    {
        $row = $this->qOne("
            SELECT SUM(table_rows) AS c
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$this->database()]);

        return (int)($row->c ?? 0);
    }

    public function charset(): ?string
    {
        $row = $this->qOne("
            SELECT default_character_set_name AS charset
            FROM information_schema.schemata
            WHERE schema_name = ?
        ", [$this->database()]);

        return $row->charset ?? null;
    }

    public function collation(): ?string
    {
        $row = $this->qOne("
            SELECT default_collation_name AS collation
            FROM information_schema.schemata
            WHERE schema_name = ?
        ", [$this->database()]);

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
        ", [$this->database()]);

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

    public function hasTable(string $table): bool
    {
        return $this->schema()->hasTable($table);
    }

    public function getColumns(string $table): array
    {
        return $this->schema()->getColumns($table);
    }

    public function getIndexes(string $table): array
    {
        return $this->schema()->getIndexes($table);
    }

    public function getTableRowsCount(string $table, string $type = 'schema'): int
    {
        if ($type === 'schema') {
            return (int)$this->qOne("
                SELECT table_rows AS c
                FROM information_schema.tables
                WHERE table_schema = ?
                AND table_name = ?;
            ", [$this->database(), $table])->c;
        }

        if ($type === 'db') {
            return $this->db()->table($table)->count();
        }

        return 0;
    }

    public function browse(string $table, int $page, int $perPage, ?string $orderBy = null, ?string $orderType = null): array
    {
        $query = $this->db()->table($table);

        $offset = ($page - 1) * $perPage;

        if ($orderBy) {
            $query->orderBy($orderBy, $orderType);
        }

        $data = $query->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'data' => $data->toArray(),
            'total' => $this->getTableRowsCount($table),
        ];
    }

    public function affectingStatement(string $query): array
    {
        $start = microtime(true);

        $affected = $this->db()->affectingStatement($query);

        return [
            'affected' => $affected,
            'time' => (int)((microtime(true) - $start) * 1000),
        ];
    }

    public function select(string $query, int $page, int $perPage): array
    {
        $start = microtime(true);
        $userHasLimit = preg_match('/\bLIMIT\b/i', $query);

        if ($userHasLimit) {
            $rows = $this->q($query);
            $rows = json_decode(json_encode($rows), true);

            return [
                'rows' => $rows,
                'total' => count($rows),
                'page' => 1,
                'per_page' => count($rows),
                'time' => (int)((microtime(true) - $start) * 1000),
            ];
        }

        $cleanSql = preg_replace('/\bLIMIT\b[\s\S]*/i', '', $query);
        $cleanSql = str_replace(';', '', $cleanSql);

        $count = $this->qOne("SELECT COUNT(*) as total FROM ($cleanSql) AS t")->total ?? 0;

        $offset = ($page - 1) * $perPage;

        $rows = $this->q("$cleanSql LIMIT $perPage OFFSET $offset");
        $rows = json_decode(json_encode($rows), true);

        return [
            'rows' => $rows,
            'total' => $count,
            'page' => $page,
            'per_page' => $perPage,
            'time' => (int)((microtime(true) - $start) * 1000),
        ];
    }
}