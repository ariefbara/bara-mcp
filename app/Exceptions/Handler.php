<?php

namespace App\Exceptions;

use Doctrine\ORM\NoResultException;
use Illuminate\ {
    Auth\Access\AuthorizationException,
    Database\Eloquent\ModelNotFoundException,
    Validation\ValidationException
};
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Resources\Exception\RegularException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        RegularException::class,
        NoResultException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof RegularException) {
            $data = [
                "meta" => [
                    "code" => $exception->getCode(),
                    "type" => $exception->getMessage(),
                    "error_detail" => $exception->getErrorDetail(),
                ],
            ];
            return response()->json($data, $exception->getCode());
        }
        if ($exception instanceof NoResultException) {
            $data = [
                "meta" => [
                    "code" => 404,
                    "type" => "Not Found",
                    "error_detail" => "not found: entity not found",
                ],
            ];
            return response()->json($data, 404);
        }
        return parent::render($request, $exception);
    }
}
