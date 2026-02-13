<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of exceptions that do not need to be reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * The list of inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(function (UnauthorizedException $e, Request $request) {

            // Web: redirige con mensaje
            if (! $request->expectsJson()) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'No tienes permisos para entrar a Administración.');
            }

            // API/JSON
            return response()->json([
                'message' => 'No autorizado.',
            ], Response::HTTP_FORBIDDEN);
        });
    }
}
