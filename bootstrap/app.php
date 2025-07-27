<?php

// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        $middleware->api(prepend: [
            // \Tymon\JWTAuth\Http\Middleware\Authenticate::class, // Dihapus dari sini
        ]);
    })
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])
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

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->wantsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;

                return response()->json([
                    'success'   => false,
                    'message'   => 'Too many requests. Please try again in '.$retryAfter.' seconds.',
                    'retry_after_seconds' => $retryAfter,
                ], 429);
            }
            return null;
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

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
            return null;
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Method not allowed.',
                ], 405);
            }
            return null;
        });

        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);
            }
            return null;
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->wantsJson()) {
                Log::error('Internal Server Error', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'message' => 'Internal Server Error.',
                ], 500);
            }
            return null;
        });

    })->create();
