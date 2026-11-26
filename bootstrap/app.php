<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureStaffIsActive;
use App\Http\Middleware\EnsureUserHasRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'active.staff' => EnsureStaffIsActive::class,
            'role' => EnsureUserHasRole::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            return match ($request->user()?->role) {
                'admin' => route('admin.dashboard'),
                'staff' => route('staff.dashboard'),
                default => route('customer.dashboard'),
            };
        });

        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
