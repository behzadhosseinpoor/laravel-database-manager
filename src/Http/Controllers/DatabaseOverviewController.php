<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseOverviewController extends Controller
{
    public function index(string $connection): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        return ApiResponse::json(
            200,
            result: [
                'connection' => $connection,
                'driver' => $driver->name(),
                'version' => $driver->version(),
                'size' => $driver->size(),
                'stats' => [
                    'table_count' => $driver->tableCount(),
                    'view_count' => $driver->viewCount(),
                    'column_count' => $driver->columnCount(),
                    'index_count' => $driver->indexCount(),
                    'primary_keys' => $driver->primaryKeyCount(),
                    'unique_indexes' => $driver->uniqueIndexCount(),
                    'foreign_keys' => $driver->foreignKeyCount(),
                    'triggers' => $driver->triggerCount(),
                    'procedures' => $driver->procedureCount(),
                    'functions' => $driver->functionCount(),
                    'total_rows' => $driver->totalRows(),
                    'charset' => $driver->charset(),
                    'collation' => $driver->collation(),
                    'uptime_seconds' => $driver->uptimeSeconds(),
                ],
            ],
        );
    }
}
