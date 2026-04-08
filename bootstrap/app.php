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
        //
        $middleware->alias([
            'check.profile' => \App\Http\Middleware\CheckProfileSetup::class,
            'admin' => \App\Http\Middleware\AdminMiddleWare::class,
            'revalidate' => \App\Http\Middleware\PreventBackHistory::class,
            'user_only' => \App\Http\Middleware\UserMiddleware::class,
            'admin_or_staff' => \App\Http\Middleware\CheckAdminOrStaff::class, 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();