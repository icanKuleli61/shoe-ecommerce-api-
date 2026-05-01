<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use App\Enums\ErrorCode;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => ErrorCode::VALIDATION_ERROR->value,
                    'message' => ErrorCode::VALIDATION_ERROR->message(),
                    'fields' => $e->errors()
                ]
            ], ErrorCode::VALIDATION_ERROR->status());
        }

        return parent::render($request, $e);
    }
}