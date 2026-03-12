<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
      \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);

    $middleware->alias([
      'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
    ]);

    $middleware->web([
      \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
    //

    $middleware->validateCsrfTokens(except: [
      '/api/donations/create-payment',
      '/webhook/paymongo',
      '/api/recommendations/match',
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    // Handle CSRF token mismatch (419 errors)
    $exceptions->render(function (TokenMismatchException $e, $request) {
      // For AJAX/Inertia requests
      if ($request->expectsJson() || $request->header('X-Inertia')) {
        return response()->json([
          'message' => 'Your session has expired. Please refresh the page and try again.'
        ], 419);
      }
            
      // For all web form submissions
      return redirect()
        ->back()
        ->withInput($request->except('password', 'password_confirmation'))
        ->with('error', 'Your session has expired. Please try again.');
    });
  })
  ->create();
