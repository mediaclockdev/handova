<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Force JSON for API routes
        if ($request->is('api/*')) {
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                if (!$request->bearerToken()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Token is required.'
                    ], 401);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired token.'
                ], 401);
            }
        }

        return parent::render($request, $exception);
    }
}
