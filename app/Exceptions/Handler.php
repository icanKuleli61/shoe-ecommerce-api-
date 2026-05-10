<?php

namespace App\Exceptions;

use Throwable;
use App\Enums\ErrorCode;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {

            return response()->json([

                'success' => false,

                'error' => [

                    'code' =>
                        ErrorCode::VALIDATION_ERROR->value,

                    'message' =>
                        collect($e->errors())
                            ->flatten()
                            ->first(),

                    'fields' =>
                        $e->errors()
                ]

            ], 422);
        }

        return parent::render($request, $e);
    }
}