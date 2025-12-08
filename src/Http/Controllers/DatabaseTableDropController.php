<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Http\Requests\DropRequest;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseTableDropController extends Controller
{
    public function index(DropRequest $request, string $connection, string $table): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        $driver->drop($table, $request->boolean('foreign_key_check'));

        return ApiResponse::json(
            200,
            'The table has been dropped successfully.'
        );
    }
}
