<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\ForcePasswordChange;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
        $middleware->appendToGroup('web', ForcePasswordChange::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Background requests (the Exam Generator, Collaboration, …) show the response's
        // "message" in a toast, so it must be plain language, never Laravel's technical
        // text ("CSRF token mismatch.", "No query results for model …") or, on a crash,
        // the exception itself. Full pages use resources/views/errors instead.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->expectsJson() || $e instanceof ValidationException) {
                return null;
            }

            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'You have been signed out. Please log in again.'], 401);
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $own = $e instanceof HttpExceptionInterface ? trim($e->getMessage()) : '';
            $technical = $own === '' || preg_match('/^(CSRF token mismatch|This action is unauthorized|No query results|Not Found|The route .* could not be found|Too Many Attempts)/i', $own);

            $message = match (true) {
                $status === 419 => 'Your session timed out. Please refresh the page and try again.',
                $status >= 500 => 'Something went wrong on our side. Please try again in a moment. If it keeps happening, contact the administrator.',
                ! $technical => $own, // a message written for the user, e.g. abort(422, "This exam isn't finished yet…")
                $status === 403 => 'You don\'t have permission to do that.',
                $status === 404 => 'This item could not be found. It may have been removed, so please refresh the page.',
                $status === 413 => 'The file is too large to upload.',
                $status === 429 => 'Too many requests at once. Please wait a moment and try again.',
                default => 'Your request couldn\'t be completed. Please refresh the page and try again.',
            };

            return response()->json(['message' => $message], $status);
        });
    })->create();