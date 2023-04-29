<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

const RESPONSE_STATUS_SUCCESSFUL = 1;
const RESPONSE_STATUS_FAILED     = 0;

const HTTP_STATUS_CODE_OK              = 200;
const HTTP_STATUS_CODE_CREATED         = 201;
const HTTP_STATUS_CODE_NO_CONTENT      = 204;
const HTTP_STATUS_CODE_REDIRECT        = 301;
const HTTP_STATUS_CODE_BAD_REQUEST     = 400;
const HTTP_STATUS_CODE_UNAUTHENTICATED = 401;
const HTTP_STATUS_CODE_UNAUTHORIZED    = 403;
const HTTP_STATUS_CODE_NOT_FOUND       = 404;
const HTTP_STATUS_CODE_UNPROCESSABLE   = 422;
const HTTP_STATUS_CODE_SYSTEM_ERROR    = 500;

function successfulResponse ($data = [], string $message = 'Successful',
                             int $httpStatusCode = Response::HTTP_OK,
                             array $additional = []) : JsonResponse
{
    $response = array_merge(
        [
            'status'  => RESPONSE_STATUS_SUCCESSFUL,
            'message' => empty($message) ? 'Successful' : $message,
            'data'    => $data,
        ],
        $additional
    );

    return response()->json($response, $httpStatusCode);
}

function successfulResponseWithToken (string $token) : JsonResponse
{
    $payload                = auth('api')->payload();
    $tokenExpireAtTimestamp = $payload->get('exp');
    $tokenExpireAt          = Carbon::parse($tokenExpireAtTimestamp)->toDateTimeString();

    $data = [
        'access_token'               => $token,
        'token_type'                 => 'Bearer',
        'token_expired_at'           => $tokenExpireAt,
        'token_expired_at_timestamp' => $tokenExpireAtTimestamp,
    ];

    return successfulResponse($data);
}

function successfulResponseWithPaginator (LengthAwarePaginator $paginator, string $message = 'Successful',
                                          int                  $httpStatusCode = HTTP_STATUS_CODE_OK) : JsonResponse
{
    [$meta, $links] = generateMetaAndLinksOfPaginator($paginator);

    return successfulResponse([], $message, $httpStatusCode,
                              ['data' => $paginator->items(), 'links' => $links, 'meta' => $meta]);
}

function successfulResponseWithApiCollection ($data, string $message = 'Successful')
{
    $data->additional(['status'  => RESPONSE_STATUS_SUCCESSFUL,
                       'message' => empty($message) ? 'Successful' : $message,]);
    return $data;
}

function successfulResponseWithoutData () : JsonResponse
{
    return successfulResponse(null, '', Response::HTTP_NO_CONTENT);
}

function successfulResponseCreated () : JsonResponse
{
    return successfulResponse([], '', HTTP_STATUS_CODE_CREATED);
}

function successfulResponseButNotFound () : JsonResponse
{
    return successfulResponse([], 'Not found', HTTP_STATUS_CODE_NOT_FOUND);
}

function failedResponse (?array $errors = [], string $message = 'Failed',
                         int    $httpStatusCode = HTTP_STATUS_CODE_SYSTEM_ERROR,
                         array  $additional = []) : JsonResponse
{
    $response = array_merge(
        [
            'status'  => RESPONSE_STATUS_FAILED,
            'message' => empty($message) ? 'Failed' : $message,
            'errors'  => $errors,
        ],
        $additional
    );

    return response()->json($response, $httpStatusCode);
}

function generateMetaAndLinksOfPaginator (LengthAwarePaginator $paginator) : array
{
    $meta = [
        'current_page' => $paginator->currentPage(),
        'from'         => $paginator->firstItem(),
        'last_page'    => $paginator->lastPage(),
        'links'        => $paginator->linkCollection(),
        'path'         => request()->url(),
        'per_page'     => $paginator->perPage(),
        'to'           => $paginator->lastItem(),
        'total'        => $paginator->total(),
    ];

    $links = [
        'first' => $paginator->url(1),
        'last'  => $paginator->url($paginator->lastPage()),
        'prev'  => $paginator->previousPageUrl(),
        'next'  => $paginator->nextPageUrl(),
    ];

    return [$meta, $links,];
}