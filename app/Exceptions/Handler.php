<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): mixed
    {
        // 1. Model not found (findOrFail, firstOrFail)
        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::error(
                message: 'Resource not found.',
                code: 404,
            );
        }

        // 2. Route not found
        if ($e instanceof NotFoundHttpException) {
            return ApiResponse::error(
                message: 'Endpoint not found.',
                code: 404,
            );
        }

        // 3. Validation errors
        if ($e instanceof ValidationException) {
            return ApiResponse::error(
                message: 'Validation failed.',
                errors: $e->errors(),
                code: 422,
            );
        }

        // 4. Unauthenticated (no token / invalid token)
        if ($e instanceof AuthenticationException) {
            return ApiResponse::error(
                message: 'Unauthenticated. Please login.',
                code: 401,
            );
        }

        // 5. Wrong HTTP method (POST on a GET route etc.)
        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::error(
                message: 'Method not allowed.',
                code: 405,
            );
        }

        // 6. Any other unhandled exception
        if (config('app.debug')) {
            return parent::render($request, $e); // show full error in local
        }

        return ApiResponse::error(
            message: 'Server error. Please try again later.',
            code: 500,
        );
    }
}
