<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->renderable(function (AuthorizationException $e, $request): ?JsonResponse {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error('Forbidden.', null, 403);
        });

        $this->renderable(function (NotFoundHttpException $e, $request): ?JsonResponse {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error($e->getMessage() ?: 'Not found.', null, 404);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return ApiResponse::error(
            $exception->getMessage(),
            $exception->errors(),
            $exception->status
        );
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (! $request->expectsJson() && ! $request->is('api/*')) {
            return parent::unauthenticated($request, $exception);
        }

        return ApiResponse::error('Unauthenticated.', null, 401);
    }
}
