<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Middleware;

use BehzadHosseinPoor\DatabaseManager\DatabaseManager;
use BehzadHosseinPoor\DatabaseManager\Exceptions\ForbiddenException;
use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!DatabaseManager::check($request)) {
            throw ForbiddenException::make();
        }

        return $next($request);
    }
}