<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->web(prepend: [
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'check.division.capacity' => \App\Http\Middleware\CheckDivisionCapacity::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'check.user.role' => \App\Http\Middleware\CheckUserRole::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Authentication Exceptions
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.',
                    'error' => 'unauthorized'
                ], 401);
            }
            
            // Redirect to login for web requests
            if ($request->is('web/*') || !$request->is('api/*')) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }
        });

        // Handle 404 Not Found Errors
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 404) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                        'error' => 'not_found'
                    ], 404);
                }
            }
        });

        // Handle 403 Forbidden Errors
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied. You do not have permission to access this resource.',
                        'error' => 'forbidden'
                    ], 403);
                }
            }
        });

        // Handle 419 Page Expired (CSRF)
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your session has expired. Please refresh the page and try again.',
                        'error' => 'session_expired'
                    ], 419);
                }
            }
        });

        // Handle 429 Too Many Requests (Rate Limiting)
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 429) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please wait a moment before trying again.',
                        'error' => 'rate_limited'
                    ], 429);
                }
            }
        });

        // Handle 500 Server Errors
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 500) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Server error. Please try again later.',
                        'error' => 'server_error'
                    ], 500);
                }
            }
        });

        // Handle all other HTTP exceptions for API
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred.',
                    'error' => 'http_error',
                    'status_code' => $e->getStatusCode()
                ], $e->getStatusCode());
            }
        });
    })
    ->create();