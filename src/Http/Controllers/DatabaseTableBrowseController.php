<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

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

        return response()->json([
            'data' => $driver->browse($table, $page, $perPage, $orderBy, $orderType),
            'total' => $driver->getTableRowsCount($table, 'db'),
        ]);
    }
}
