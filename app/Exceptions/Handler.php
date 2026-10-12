<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (! config('app.debug')
            && ! $request->expectsJson()
            && ! $response->isRedirection()
            && $response->getStatusCode() >= 400) {
            $statusCode = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : $response->getStatusCode();

            return response()->view(
                'errors.something-went-wrong',
                ['statusCode' => $statusCode],
                $statusCode >= 400 ? $statusCode : 500
            );
        }

        return $response;
    }
}
