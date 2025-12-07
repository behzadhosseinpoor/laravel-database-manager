<?php

namespace BehzadHosseinPoor\DatabaseManager\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponse
{
    public static function json(
        int                     $status,
        ?string                 $message = null,
        null|array|JsonResource $result = null,
    ): JsonResponse
    {
        $response = [
            'status' => $status
        ];

        if (!is_null($message)) {
            $response['message'] = $message;
        }

        if (!is_null($result)) {
            $response['result'] = $result;
        }

        return response()->json(
            $response,
            $status,
            [
                'Content-Type' => 'application/json;charset=UTF-8',
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    public static function query(
        string  $type,
        bool    $ok = true,
        array   $columns = [],
        array   $rows = [],
        ?string $message = null,
        ?string $code = null,
        ?int    $page = null,
        ?int    $perPage = null,
        ?int    $total = null,
        ?int    $affected = null,
        ?int    $time = null,
    ): JsonResponse
    {
        $response = [
            'status' => 200,
            'message' => null,
        ];

        $response['result'] = [
            'ok' => $ok,
            'type' => $type,
            'message' => $message,
            'code' => $code,
            'columns' => $columns,
            'rows' => $rows,
            'total' => $total,
            'affected' => $affected,
            'page' => $page,
            'per_page' => $perPage,
            'time_ms' => $time,
        ];

        return response()->json(
            $response,
            $response['status'],
            [
                'Content-Type' => 'application/json;charset=UTF-8',
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}