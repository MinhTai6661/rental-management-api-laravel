<?php

use App\Http\Middleware\CheckPermission;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'can_do' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return ApiResponse::error(__('auth.unauthenticated'), 401);
                }

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return ApiResponse::error($e->validator->errors()->first(), 422);
                }

                if (
                    $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ||
                    $e->getPrevious() instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                ) {
                    return ApiResponse::error(__('general.not_found'), 404);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return ApiResponse::error(__('general.not_found'), 404);
                }

                return ApiResponse::error($e->getMessage() ?: 'Error', method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
            }

            return $response;
        });
    })->create();
