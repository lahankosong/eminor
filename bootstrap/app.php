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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin'    => \App\Http\Middleware\IsAdmin::class,
            'trackvisit' => \App\Http\Middleware\TrackPageVisit::class,
        ]);
        // Guest yang akses route ber-auth diarahkan ke landing page EMINOR
        // (timer 2 menit di landing akan memandu mereka login secara natural)
        $middleware->redirectGuestsTo(fn () => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
