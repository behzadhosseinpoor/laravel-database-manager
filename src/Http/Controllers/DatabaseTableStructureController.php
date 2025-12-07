<?php
/** @noinspection PhpUnused */

/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseTableStructureController extends Controller
{
    public function index(string $connection, string $table): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        return ApiResponse::json(
            200,
            result: [
                'columns' => $driver->getColumns($table),
                'indexes' => $driver->getIndexes($table),
            ],
        );
    }
}
