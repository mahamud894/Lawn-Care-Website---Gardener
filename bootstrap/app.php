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
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'gardener' => \App\Http\Middleware\EnsureUserIsGardener::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsNotBlocked::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        // SSLCommerz posts back to these URLs from its own site
        $middleware->validateCsrfTokens(except: [
            'payment/sslcommerz/*',
        ]);

        // Logged-in users visiting /login or /signup go to the home page
        $middleware->redirectUsersTo('/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
