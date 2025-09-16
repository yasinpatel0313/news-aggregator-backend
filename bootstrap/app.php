<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
       // Register your JWT middleware here
       $middleware->alias([
           'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
       ]);
     })
     ->withExceptions(function (Exceptions $exceptions) {
       // Handle 500 errors for API routes
       $exceptions->render(function (\Throwable $e, $request) {
           if ($request->is('api/*')) {

               return response()->json([
                   'success' => false,
                   'message' => 'Internal server error',
                   'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
               ], 500);
           }
       });
     })
    ->create();
