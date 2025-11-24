<?php

use App\Http\Middleware\EnsureUserIsVerified;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsManager;
use App\Http\Middleware\IsSalesman;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'verified' => EnsureUserIsVerified::class,
        'isAdmin' => IsAdmin::class,
        'isManager' => IsManager::class,
        'isSalesman' => IsSalesman::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
