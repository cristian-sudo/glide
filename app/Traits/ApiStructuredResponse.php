<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiStructuredResponse
{
    protected function successResponse($message, $data = [], $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'statusCode' => $statusCode,
        ], $statusCode);
    }

    protected function errorResponse($message, $errors = [], $statusCode = 422): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'statusCode' => $statusCode,
        ], $statusCode);
    }
}
