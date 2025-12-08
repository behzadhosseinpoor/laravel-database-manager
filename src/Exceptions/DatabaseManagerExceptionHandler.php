<?php

namespace BehzadHosseinPoor\DatabaseManager\Exceptions;

use BehzadHosseinPoor\DatabaseManager\Helpers\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DatabaseManagerExceptionHandler extends BaseHandler
{
    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($request->is(config('database-manager.path') . '/*')) {
            $code = $this->getCode($e);

            return ApiResponse::json(
                $code,
                $this->getMessage($e),
            );
        }

        return parent::render($request, $e);
    }

    protected function getCode(Throwable $e): int
    {
        $code = 500;

        if (method_exists($e, 'getStatusCode')) {
            $code = $e->getStatusCode();
        }

        return $code;
    }

    protected function getMessage(Throwable $e): string
    {
        return $e->getMessage() ?? 'Error!!!';
    }
}
