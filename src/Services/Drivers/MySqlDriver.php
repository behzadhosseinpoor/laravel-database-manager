<?php
/** @noinspection PhpUnhandledExceptionInspection */
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

    /* -------------------------------------------------------------------------
     | Core Helpers
     |-------------------------------------------------------------------------- */

    protected function db(): MySqlConnection
    {
        return DB::connection($this->conn);
    }

    protected function schema(): MySqlBuilder
    {
        return Schema::connection($this->conn);
    }

    protected function qOne(string $sql, array $params = []): ?object
    {
        return $this->db()->selectOne($sql, $params);
    }

    protected function q(string $query, array $bindings = []): array
    {
        return $this->db()->select($query, $bindings);
    }

    public function database(): string
    {
        return $this->db()->getDatabaseName();
    }

    public function name(): string
    {
        return 'mysql';
    }

    /* -------------------------------------------------------------------------
     | Metadata: Version, Size, Counts
     |-------------------------------------------------------------------------- */

    public function version(): string
    {
        return (string)$this->qOne("SELECT VERSION() AS v")->v;
    }

    public function size(): int
    {
        return (int)$this->db()->table('information_schema.tables')
            ->where('table_schema', $this->database())
            ->selectRaw('SUM(data_length + index_length) AS size')
            ->value('size');
    }

    public function tableCount(): int
    {
        return $this->db()->table('information_schema.tables')
            ->where('table_schema', $this->database())
            ->where('table_type', 'BASE TABLE')
            ->count();
    }

    public function viewCount(): int
    {
        return $this->db()->table('information_schema.tables')
            ->where('table_schema', $this->database())
            ->where('table_type', 'VIEW')
            ->count();
    }

    public function columnCount(): int
    {
        return $this->db()->table('information_schema.columns')
            ->where('table_schema', $this->database())
            ->count();
    }

    public function indexCount(): int
    {
        return $this->db()->table('information_schema.statistics')
            ->where('table_schema', $this->database())
            ->where('index_name', '!=', 'PRIMARY')
            ->distinct()
            ->count('index_name');
    }

    public function primaryKeyCount(): int
    {
        return $this->db()->table('information_schema.statistics')
            ->where('table_schema', $this->database())
            ->where('index_name', 'PRIMARY')
            ->count();
    }

    public function uniqueIndexCount(): int
    {
        return $this->db()->table('information_schema.statistics')
            ->where('table_schema', $this->database())
            ->where('NON_UNIQUE', 0)
            ->where('index_name', '!=', 'PRIMARY')
            ->distinct()
            ->count('index_name');
    }

    public function foreignKeyCount(): int
    {
        return $this->db()->table('information_schema.KEY_COLUMN_USAGE')
            ->where('table_schema', $this->database())
            ->whereNotNull('referenced_table_name')
            ->count();
    }

    public function triggerCount(): int
    {
        return $this->db()->table('information_schema.triggers')
            ->where('trigger_schema', $this->database())
            ->count();
    }

    public function procedureCount(): int
    {
        return $this->db()->table('information_schema.routines')
            ->where('routine_schema', $this->database())
            ->where('routine_type', 'PROCEDURE')
            ->count();
    }

    public function functionCount(): int
    {
        return $this->db()->table('information_schema.routines')
            ->where('routine_schema', $this->database())
            ->where('routine_type', 'FUNCTION')
            ->count();
    }

    public function totalRows(): int
    {
        return (int)$this->db()->table('information_schema.tables')
            ->where('table_schema', $this->database())
            ->sum('table_rows');
    }

    public function charset(): ?string
    {
        return $this->db()->table('information_schema.schemata')
            ->where('schema_name', $this->database())
            ->value('default_character_set_name');
    }

    public function collation(): ?string
    {
        return $this->db()->table('information_schema.schemata')
            ->where('schema_name', $this->database())
            ->value('default_collation_name');
    }

    /* -------------------------------------------------------------------------
     | Server Status
     |-------------------------------------------------------------------------- */

    public function uptimeSeconds(): int
    {
        return (int)($this->qOne("SHOW GLOBAL STATUS LIKE 'Uptime'")->Value ?? 0);
    }

    public function activeConnections(): int
    {
        return (int)($this->qOne("SHOW STATUS WHERE variable_name = 'Threads_connected'")->Value ?? 0);
    }

    /* -------------------------------------------------------------------------
     | Table List
     |-------------------------------------------------------------------------- */

    public function tables(): array
    {
        $rows = DB::table('information_schema.tables')
            ->where('table_schema', $this->database())
            ->orderBy('table_name')
            ->get([
                'table_name AS name',
                'table_rows AS row_count',
                'engine AS engine',
                'table_collation AS collation',
                'data_length AS data_length',
                'index_length AS index_length'
            ]);

        return $rows->map(function ($t) {
            return [
                'name' => $t->name,
                'rows' => (int)$t->row_count,
                'engine' => $t->engine,
                'collation' => $t->collation,
                'size' => (int)($t->data_length + $t->index_length),
            ];
        })->toArray();
    }

    /* -------------------------------------------------------------------------
     | Structure (Columns / Indexes)
     |-------------------------------------------------------------------------- */

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
        return match ($type) {
            'schema' => (int)$this->db()->table('information_schema.tables')
                ->where('table_schema', $this->database())
                ->where('table_name', $table)
                ->value('table_rows'),
            'db' => $this->db()->table($table)->count(),
            default => 0,
        };
    }

    /* -------------------------------------------------------------------------
     | Browse & Select
     |-------------------------------------------------------------------------- */

    public function browse(string $table, int $page, int $perPage, ?string $orderBy = null, ?string $orderType = null): array
    {
        $query = $this->db()->table($table);

        if ($orderBy) {
            $query->orderBy($orderBy, $orderType);
        }

        $offset = ($page - 1) * $perPage;

        $data = $query->offset($offset)->limit($perPage)->get();

        return [
            'data' => $this->fixBinary($data->all()),
            'total' => $this->getTableRowsCount($table, 'db'),
        ];
    }

    public function affectingStatement(string $query): int
    {
        return $this->db()->affectingStatement($query);
    }

    public function select(string $query, int $page, int $perPage): array
    {
        $hasLimit = preg_match('/\bLIMIT\b/i', $query);

        if ($hasLimit) {
            $rows = $this->fixBinary($this->q($query));

            return [
                'rows' => $rows,
                'total' => count($rows),
            ];
        }

        $clean = preg_replace('/\bLIMIT\b[\s\S]*/i', '', $query);
        $clean = str_replace(';', '', $clean);

        $countQuery = DB::connection($this->conn)
            ->selectOne("SELECT COUNT(*) AS total FROM ($clean) AS t");

        $total = (int)($countQuery->total ?? 0);

        $offset = ($page - 1) * $perPage;

        $rows = $this->q("$clean LIMIT $perPage OFFSET $offset");

        return [
            'rows' => $this->fixBinary($rows),
            'total' => $total,
        ];
    }

    public function drop(string $table, bool $foreignKeyCheck): void
    {
        try {
            if ($foreignKeyCheck === false) {
                $this->db()->statement('SET FOREIGN_KEY_CHECKS = 0;');
            }

            $this->schema()->drop($table);
        } finally {
            if ($foreignKeyCheck === false) {
                $this->db()->statement('SET FOREIGN_KEY_CHECKS = 1;');
            }
        }
    }

    public function truncate(string $table, bool $foreignKeyCheck): void
    {
        try {
            if ($foreignKeyCheck === false) {
                $this->db()->statement('SET FOREIGN_KEY_CHECKS = 0;');
            }

            $this->db()->table($table)->truncate();
        } finally {
            if ($foreignKeyCheck === false) {
                $this->db()->statement('SET FOREIGN_KEY_CHECKS = 1;');
            }
        }
    }

    /* -------------------------------------------------------------------------
     | Internal Helpers
     |-------------------------------------------------------------------------- */

    protected function fixBinary(array $data): array
    {
        return collect($data)->map(function ($row) {
            return collect((array)$row)->map(function ($value) {
                if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                    return ':::___DATABASE___MANAGER___ENCODED___:::' . base64_encode($value);
                }

                return $value;
            })->toArray();
        })->toArray();
    }
}
