<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Http\Requests\QueryRequest;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use BehzadHosseinPoor\DatabaseManager\Services\Drivers\DatabaseDriver;
use Illuminate\Http\JsonResponse;

class DatabaseQueryController extends Controller
{
    public function index(QueryRequest $request, string $connection): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        $query = trim($request->input('query'));
        $page = max(1, (int)$request->input('page', 1));
        $perPage = max(10, (int)$request->input('per_page', 10));

        if (substr_count($query, ';') > 1) {
            return ApiResponse::json(
                status: 400,
                message: 'Multiple statements are not allowed.',
            );
        }

        $isSelect = preg_match('/^\s*SELECT/i', $query);

        if ($isSelect) {
            return $this->runSelect($driver, $query, $page, $perPage);
        } else {
            $affected = $driver->affectingStatement($query);

            return ApiResponse::json(
                status: 200,
                message: "Query executed successfully — affected rows: $affected",
            );
        }
    }

    private function runSelect(DatabaseDriver $driver, string $query, int $page, int $perPage): JsonResponse
    {
        $select = $driver->select($query, $page, $perPage);
        $columns = $this->extractColumns($select['rows'][0] ?? []);

        return ApiResponse::json(
            status: 200,
            result: [
                'columns' => $columns,
                'rows' => $select['rows'],
                'total' => $select['total'],
            ],
        );
    }

    private function extractColumns(array $row): array
    {
        if (!$row) return [];

        return array_keys($row);
    }
}
