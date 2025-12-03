<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Middleware;

use BehzadHosseinPoor\DatabaseManager\DatabaseManager;
use BehzadHosseinPoor\DatabaseManager\Exceptions\ForbiddenException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|null
     */
    public function handle(Request $request, Closure $next): Response|null
    {
        if (!DatabaseManager::check($request)) {
            throw ForbiddenException::make();
        }

        return $next($request);
    }
}