<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\TrackVisitorSession;
use App\Http\Middleware\InstitutionMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\EnsureActiveSubscription;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Middleware Aliases
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'institution' => InstitutionMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'subscription' => EnsureActiveSubscription::class,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Web Middleware
        |--------------------------------------------------------------------------
        */

        $middleware->appendToGroup(
            'web',
            TrackVisitorSession::class
        );
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();