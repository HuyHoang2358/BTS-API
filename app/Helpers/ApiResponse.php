<?php
namespace App\Helpers;

use App\Enums\ApiMessage;
use Illuminate\Http\JsonResponse;
class ApiResponse
{
    public static function success($data = [], ApiMessage $apiMessage = ApiMessage::SUCCESS, $status = 200): JsonResponse
    {
        return response()->json([
            'code' => $apiMessage->code(),
            'message' => $apiMessage->message(),
            'data' => $data
        ], $status);
    }

    public static function error($errors = [], ApiMessage $apiMessage = ApiMessage::ERROR, $status = 400): JsonResponse
    {
        return response()->json([
            'code' => $apiMessage->code(),
            'message' => $apiMessage->message(),
            'errors' => $errors
        ], $status);
    }
}
