<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('loginId')) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || Cache::get('active_session_id:' . $user->id) !== $request->session()->getId()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
