<?php

namespace App\Services;

abstract class BaseService
{
    protected function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function errorResponse(string $message = 'Error', mixed $data = null, int $code = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
