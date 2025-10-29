<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            $prev = $e->getPrevious();
            if ($prev instanceof ModelNotFoundException) {
                $model = class_basename($prev->getModel());
                return response()->json([
                    'message' => "{$model} not found",
                    'code'    => 'NOT_FOUND',
                    'errors'  => ['ids' => $prev->getIds()],
                ], 404);
            }

            return response()->json([
                'message' => 'Route not found',
                'code'    => 'ROUTE_NOT_FOUND',
            ], 404);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'message' => 'Validation failed',
                'code'    => 'VALIDATION_ERROR',
                'errors'  => $e->errors(),
            ], 422);
        });
    })
    ->create();
