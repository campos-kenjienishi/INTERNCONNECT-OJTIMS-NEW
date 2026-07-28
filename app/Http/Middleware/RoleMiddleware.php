<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more role values as parameters.
     * Usage in routes: middleware('role:0') or middleware('role:0,1')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed role values
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
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

        if (!in_array((string) $user->role, $roles, true)) {
            return $this->redirectToDashboard($user);
        }

        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }

    /**
     * Redirect unauthorized users back to their own dashboard.
     */
    private function redirectToDashboard(?User $user): Response
    {
        if (!$user) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        return match ((string) $user->role) {
            '0' => redirect()->route('student_home'),
            '1' => redirect('/dashboard'),
            '2' => redirect()->route('professor_home'),
            default => response()->view('errors.something-went-wrong', ['statusCode' => 401], 401),
        };
    }
}
