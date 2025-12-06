<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace BehzadHosseinPoor\DatabaseManager\Http\Middleware;

use BehzadHosseinPoor\DatabaseManager\Exceptions\InvalidTableException;
use BehzadHosseinPoor\DatabaseManager\Services\DatabaseDriverFactory;
use Closure;
use Illuminate\Http\Request;

class ValidateTable
{
    public function handle(Request $request, Closure $next)
    {
        $connection = $request->route('connection');
        $table = $request->route('table');

        $driver = DatabaseDriverFactory::make($connection);

        if (!$driver->hasTable($table)) {
            throw InvalidTableException::make();
        }

        return $next($request);
    }
}