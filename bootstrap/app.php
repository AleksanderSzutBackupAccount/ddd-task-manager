<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Src\Identity\Infrastructure\Laravel\Http\Middleware\AuthMiddleware;
use Src\Shared\Domain\Exceptions\DomainError;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.jwt' => AuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (DomainError $e) {
            return response()->json([
                'error' => $e->errorCode(),
                'message' => $e->getMessage(),
            ], 404);
        });
    })->create();
