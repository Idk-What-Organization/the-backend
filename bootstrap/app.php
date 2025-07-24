<?php

// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->stopIgnoring(ValidationException::class);
        $exceptions->report(function (ValidationException $e) {
            if (request()->wantsJson()) {
                $duration = defined('LARAVEL_START') ? (microtime(true) - LARAVEL_START) * 1000 : null;
                Log::warning('Validation failed for registration attempt.', [
                    'ip_address'  => request()->ip(),
                    'path' => request()->path(),
                    'duration_ms' => round($duration),
                    'errors' => $e->errors(),
                ]);
            }

            return false;
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Validation Failed! Please check the data you submitted.',
                    'errors'    => $e->errors(),
                ], 422);
            }
            return null;
        });

    })->create();
