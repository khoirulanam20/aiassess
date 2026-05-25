<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

abstract class BaseController extends Controller
{
    protected function respondSuccess(mixed $data = null, string $message = 'Success', int $code = 200): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function respondError(string $message = 'Error', mixed $data = null, int $code = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
