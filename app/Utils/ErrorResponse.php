<?php

namespace App\Utils;

class ErrorResponse
{

    public static function error($message = null, $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }
}
