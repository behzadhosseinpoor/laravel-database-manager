<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseTablesController extends Controller
{
    public function index(string $connection): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        return response()->json($driver->tables());
    }
}
