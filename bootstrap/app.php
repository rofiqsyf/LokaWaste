<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ukuran file terlalu besar melebihi batas upload server (Maks. 8 MB).',
                    'errors' => ['photo' => ['Ukuran file foto terlalu besar.']]
                ], 413);
            }

            return back()->withErrors([
                'photo' => 'Ukuran file terlalu besar melebihi batas upload server. Silakan gunakan foto dengan ukuran lebih kecil.',
            ])->withInput();
        });
    })->create();
