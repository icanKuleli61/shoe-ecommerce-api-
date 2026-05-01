<?php

namespace App\Exceptions;

use Exception;
use App\Enums\ErrorCode;

class BaseException extends Exception
{
    protected ErrorCode $error;

    public function __construct(ErrorCode $error)
    {
        $this->error = $error;
        parent::__construct($error->message());
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $this->error->value,
                'message' => $this->error->message(),
            ]
        ], $this->error->status());
    }
}