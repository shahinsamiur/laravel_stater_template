<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return ApiResponse::error(message: 'Resource not found.', code: 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {

            if ($e->getPrevious() instanceof ModelNotFoundException) {
                return ApiResponse::error(message: 'Resource not found.', code: 404);
            }
            return ApiResponse::error(message: 'Endpoint not found.', code: 404);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            return ApiResponse::error(message: 'Validation failed.', errors: $e->errors(), code: 422);
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return ApiResponse::error(message: 'Unauthenticated. Please login.', code: 401);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            return ApiResponse::error(message: 'Method not allowed.', code: 405);
        });
    })->create();
