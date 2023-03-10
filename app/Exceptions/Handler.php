<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;
use ValueError;

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
    public function register()
    {
        if (!Request::wantsJson()) {
            return;
        }

        $this->renderable(function (ValidationException $exception) {
            return new JsonResponse([
                'errors' => $exception->errors()
            ]);
        });

        $this->renderable(function (ValueError $exception) {
            $fieldName = preg_match('/value for enum "+.*\b[\\\\](.*\b)/', $exception->getMessage(), $matches) ? $matches[1] : null;

            if (is_null($fieldName)) {
                throw $exception;
            }

            return new JsonResponse([
                'errors' => [
                    $fieldName => $exception->getMessage()
                ]
            ]);
        });
    }
}
