<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
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
            return redirect('/login');
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || !in_array((string) $user->role, $roles, true)) {
            return $this->redirectToDashboard($user);
        }

        return $next($request);
    }

    /**
     * Redirect unauthorized users back to their own dashboard.
     */
    private function redirectToDashboard(?User $user): Response
    {
        if (!$user) {
            return redirect('/login');
        }

        return match ((string) $user->role) {
            '0' => redirect()->route('student_home'),
            '1' => redirect('/dashboard'),
            '2' => redirect()->route('professor_home'),
            default => redirect('/login'),
        };
    }
}
