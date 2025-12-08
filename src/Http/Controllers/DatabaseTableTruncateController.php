<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Http\Requests\TruncateRequest;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseTableTruncateController extends Controller
{
    public function index(TruncateRequest $request, string $connection, string $table): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        $driver->truncate($table, $request->boolean('foreign_key_check'));

        return ApiResponse::json(
            200,
            'The table has been truncated successfully.'
        );
    }
}
