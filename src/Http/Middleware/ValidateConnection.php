<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Middleware;

use BehzadHosseinPoor\DatabaseManager\Exceptions\InvalidConnectionException;
use Closure;
use Illuminate\Http\Request;

class ValidateConnection
{
    public function handle(Request $request, Closure $next)
    {
        $connection = $request->route('connection');

        $allowed = config('database-manager.connections', []);

        if (!$connection || !in_array($connection, $allowed)) {
            throw InvalidConnectionException::make();
        }

        return $next($request);
    }
}
