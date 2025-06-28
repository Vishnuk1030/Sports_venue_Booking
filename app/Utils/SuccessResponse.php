<?php


namespace App\Utils;

class SuccessResponse
{
    public static function success($message = null, $data = [], $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
