<?php

namespace App\Http\Middleware;

use App\Services\UploadProcessingLock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateUpload
{
    public function __construct(private readonly UploadProcessingLock $uploadProcessingLock)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasFile('file')) {
            return $next($request);
        }

        $route = $request->route();
        $routeIdentifier = $route ? $route->uri() : $request->path();
        $routeParameters = $route ? $route->parameters() : [];
        ksort($routeParameters);

        $operation = $request->method().':'.$routeIdentifier.':'.json_encode($routeParameters);
        $lock = $this->uploadProcessingLock->create((int) Session::get('loginId'), $operation);

        if (!$lock->get()) {
            $message = 'This upload is already being validated. Please wait for the current request to finish.';

            return back()
                ->withInput()
                ->withErrors(['file' => $message])
                ->with('upload_error', $message);
        }

        try {
            return $next($request);
        } finally {
            $lock->release();
        }
    }
}
