<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Middleware;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DatabaseManagerExceptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            $code = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : 500;

            return ApiResponse::json(
                $code,
                $e->getMessage() ?: 'Error!!!'
            );
        }
    }
}
