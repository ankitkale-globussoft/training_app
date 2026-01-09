<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'trainer.api' => \App\Http\Middleware\IsTrainer::class,
            'trainer.web' => \App\Http\Middleware\IsTrainerWeb::class,
            'org.web' => \App\Http\Middleware\IsOrgWeb::class,
            'org.api' => \App\Http\Middleware\IsOrgApi::class,
            'student.web' => \App\Http\Middleware\IsStudentWeb::class,
            'student.api' => \App\Http\Middleware\IsStudentApi::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorized. Please login to proceed'
                ], 401);
            }
        });
    })->create();
