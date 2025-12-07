<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use BehzadHosseinPoor\DatabaseManager\Http\Requests\BrowseRequest;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseTableBrowseController extends Controller
{
    public function index(BrowseRequest $request, string $connection, string $table): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 10);
        $orderBy = $request->get('order_by');
        $orderType = $request->get('order_type', 'asc');

        return ApiResponse::json(
            200,
            result: $driver->browse($table, $page, $perPage, $orderBy, $orderType),
        );
    }
}
