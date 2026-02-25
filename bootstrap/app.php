<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Configuration\Events;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
         $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::validation(
                    'Validation failed',
                    $e->errors()
                );
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::notFound('Endpoint tidak ditemukan');
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error(
                    $e->getMessage(),
                    null,
                    500
                );
            }
        });
    })
    ->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ])
    ->create();
