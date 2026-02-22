<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OJTCoordinator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role == 1) {
            return $next($request);
        }

        // Redirect or abort the request if the user is not authorized
        return redirect()->route('login'); // You can define a route for unauthorized access
        // Alternatively, you can abort the request with a 403 Forbidden response:
        // return abort(403);
    }
}
