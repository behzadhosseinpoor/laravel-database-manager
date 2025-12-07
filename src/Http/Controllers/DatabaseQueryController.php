<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Http\Requests\QueryRequest;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use BehzadHosseinPoor\DatabaseManager\Services\Drivers\DatabaseDriver;
use Illuminate\Http\JsonResponse;
use Throwable;

class DatabaseQueryController extends Controller
{
    public function index(QueryRequest $request, string $connection): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        $query = trim($request->input('query'));
        $page = max(1, (int)$request->input('page', 1));
        $perPage = max(10, (int)$request->input('per_page', 10));

        try {
            if (substr_count($query, ';') > 1) {
                return $this->error("Multiple statements are not allowed.");
            }

            $isSelect = preg_match('/^\s*SELECT/i', $query);

            if ($isSelect) {
                return $this->runSelect($driver, $query, $page, $perPage);
            } else {
                return $this->runWrite($driver, $query);
            }

        } catch (Throwable $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    private function error(string $message, ?string $code = null): JsonResponse
    {
        return ApiResponse::query(
            type: 'error',
            ok: false,
            message: $message,
            code: $code
        );
    }

    private function runWrite(DatabaseDriver $driver, string $query): JsonResponse
    {
        $affected = $driver->affectingStatement($query);

        return ApiResponse::query(
            type: 'write',
            message: 'Query executed successfully',
            affected: $affected['affected'],
            time: $affected['time'],
        );
    }

    private function runSelect(DatabaseDriver $driver, string $query, int $page, int $perPage): JsonResponse
    {
        $select = $driver->select($query, $page, $perPage);
        $columns = $this->extractColumns($select['rows'][0]);

        return ApiResponse::query(
            type: 'select',
            columns: $columns,
            rows: $select['rows'],
            page: $select['page'],
            perPage: $select['per_page'],
            total: $select['total'],
            time: $select['time'],
        );
    }

    private function extractColumns(array $row): array
    {
        if (!$row) return [];

        return array_keys($row);
    }
}
