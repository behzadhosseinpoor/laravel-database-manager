<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Illuminate\Http\JsonResponse;

class DatabaseOverviewController extends Controller
{
    public function index(string $connection): JsonResponse
    {
        $driver = DatabaseDriverFactory::make($connection);

        return response()->json([
            'connection' => $connection,
            'driver' => $driver->name(),
            'version' => $driver->version(),
            'size' => $driver->size(),
            'stats' => $driver->stats(),
        ]);
    }
}
