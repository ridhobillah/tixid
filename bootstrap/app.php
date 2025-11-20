<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //mendaapatkan middleware yang akan di gunakan pada route route tertentu
        $middleware->alias([
            'isAdmin' => App\Http\Middleware\isAdmin::class,
            'isGuest' => App\Http\Middleware\isGuest::class,
            'isStaff' => App\Http\Middleware\isStaff::class,
            'isUser' => App\Http\Middleware\isUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
