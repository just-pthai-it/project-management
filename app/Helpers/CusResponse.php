<?php

namespace App\Helpers;


use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CusResponse
{
    public static function successful ($data = [], string $message = '', int $httpStatusCode = Response::HTTP_OK) : JsonResponse
    {
        return \response()->json(['data'    => $data,
                                  'message' => empty($message) ? 'Successful' : $message,],
                                 $httpStatusCode);
    }

    public static function failed (array $errors = [], string $message = '', int $httpStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY) : JsonResponse
    {
        return \response()->json(['errors'  => $errors,
                                  'message' => empty($message) ? 'Failed' : $message,],
                                 $httpStatusCode);
    }

}