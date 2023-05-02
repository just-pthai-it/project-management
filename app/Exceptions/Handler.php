<?php

namespace App\Exceptions;

use App\Helpers\CusResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use function App\Helpers\failedResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register () : void
    {
        $this->renderable(function (AuthenticationException $e)
        {
            return CusResponse::failed([], $e->getMessage(), Response::HTTP_UNAUTHORIZED);
        });

        $this->renderable(function (ValidationException $exception)
        {
            return CusResponse::failed($exception->validator->errors()->messages(),
                                       $exception->getMessage(), Response::HTTP_BAD_REQUEST);

        });

        $this->renderable(function (AccessDeniedHttpException $e)
        {
            return CusResponse::failed([], $e->getMessage(), Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (NotFoundHttpException $e)
        {
            if ($e->getPrevious() instanceof ModelNotFoundException)
            {
                return CusResponse::failed([], 'Resource not found', Response::HTTP_NOT_FOUND);
            }

            return CusResponse::failed([], 'API not found', Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (Throwable $e)
        {
            return CusResponse::failed([], $e->getMessage());
        });

        $this->reportable(function (Throwable $e)
        {
            //
        });
    }
}
