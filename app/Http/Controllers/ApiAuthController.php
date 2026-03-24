<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Handles OJT System authentication via external IdP callback.
 *
 * The external IdP (Single Sign-On System) redirects the user here after
 * a successful login, carrying a signed JWT in the `token` parameter.
 *
 * Callback URL:  /auth/callback
 * Methods:       GET, POST
 *
 * JWT claims expected:
 *   - userId  : user's ID in this system's `users` table
 *   - email   : user's email address
 *   - roles   : array of role strings (e.g., ["student"], ["professor"], ["coordinator"])
 *   - iss     : issuer (external auth server)
 *   - aud     : audience (this application)
 *   - azp     : authorized party (client ID)
 *   - exp     : expiration time
 *   - nbf     : not-before time
 *   - iat     : issued-at time
 */
class ApiAuthController extends Controller
{
    public function __construct(private JwtService $jwt) {}

    /**
     * Redirect user to external IdP login page.
     * Route: GET /auth/login
     */
    public function loginExternal(Request $request)
    {
        $idpConfig = config('services.idp');
        $baseUrl = rtrim((string) ($idpConfig['base_url'] ?? ''), '/');
        $mode = strtolower((string) ($idpConfig['auth_request_mode'] ?? 'login'));

        if ($mode === 'authorize') {
            $baseAuthUrl = $baseUrl . '/api/v1/auth/authorize';
            $query = array_filter([
                'client_id' => $idpConfig['client_id'] ?? null,
                'response_type' => 'code',
                'redirect_uri' => $idpConfig['callback_url'] ?? null,
                'scope' => 'openid email profile',
                'state' => bin2hex(random_bytes(16)),
            ], static fn ($value) => $value !== null && $value !== '');
        } else {
            $baseAuthUrl = (string) ($idpConfig['auth_url'] ?? '');
            if ($baseAuthUrl === '') {
                $baseAuthUrl = $baseUrl . ((string) ($idpConfig['login_path'] ?? '/login'));
            }
            $query = array_filter([
                'client_id' => $idpConfig['client_id'] ?? null,
                'redirect_uri' => $idpConfig['callback_url'] ?? null,
            ], static fn ($value) => $value !== null && $value !== '');
        }

        $loginUrl = $baseAuthUrl;
        if (!empty($query)) {
            $separator = str_contains($baseAuthUrl, '?') ? '&' : '?';
            $loginUrl .= $separator . http_build_query($query);
        }

        Log::info('API auth: redirecting to IdP login', [
            'login_url' => $loginUrl,
            'client_id' => $idpConfig['client_id'],
        ]);

        return redirect()->away($loginUrl);
    }

    /**
     * Handle GET redirect callback from the external IdP.
     * The JWT token is expected as a query parameter: `?token=<jwt>`
     */
    public function callbackGet(Request $request)
    {
        Log::info('API auth callback(get): received callback', [
            'has_code' => $request->filled('code'),
            'has_token' => $request->filled('token'),
            'has_jwt_token' => $request->filled('jwt_token'),
            'query_keys' => array_keys($request->query()),
        ]);

        if ($request->filled('token')) {
            return $this->handleCallback($request, (string) $request->query('token'));
        }
        if ($request->filled('jwt_token')) {
            return $this->handleCallback($request, (string) $request->query('jwt_token'));
        }
        if ($request->filled('code')) {
            return $this->handleCodeCallback($request, (string) $request->query('code'));
        }
        $token = $request->query('token');
        return $this->handleCallback($request, $token);
    }

    /**
     * Handle POST callback from the external IdP.
     * The JWT token is expected in the request body as `token`.
     */
    public function callbackPost(Request $request)
    {
        Log::info('API auth callback(post): received callback', [
            'has_code' => $request->filled('code'),
            'has_token' => $request->filled('token'),
            'has_jwt_token' => $request->filled('jwt_token'),
            'input_keys' => array_keys($request->all()),
        ]);

        if ($request->filled('token')) {
            return $this->handleCallback($request, (string) $request->input('token'));
        }
        if ($request->filled('jwt_token')) {
            return $this->handleCallback($request, (string) $request->input('jwt_token'));
        }
        if ($request->filled('code')) {
            return $this->handleCodeCallback($request, (string) $request->input('code'));
        }
        $request->validate(['token' => 'required|string']);
        return $this->handleCallback($request, $request->input('token'));
    }

    /**
     * Handle OAuth code callback: exchange code, fetch user info, create session.
     */
    private function handleCodeCallback(Request $request, string $code)
    {
        $idpConfig = config('services.idp');
        $clientId = $idpConfig['client_id'] ?? null;
        $clientSecret = $idpConfig['client_secret'] ?? null;
        $tokenUrl = $idpConfig['token_url'] ?? null;
        $http = Http::acceptJson();
        if (($idpConfig['verify_tls'] ?? true) === false && app()->environment(['local', 'testing'])) {
            $http = $http->withoutVerifying();
        }
        if (empty($tokenUrl)) {
            Log::warning('API auth callback(code): missing token URL configuration');
            return redirect('/login')->with('error', 'Authentication failed: token endpoint is not configured.');
        }
        $jsonMinimal = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ];
        try {
            $response = $http->post($tokenUrl, $jsonMinimal);
            $data = $response->json();
            if ($response->ok() && !empty($data['access_token'])) {
                $accessToken = (string) $data['access_token'];
                $meUrl = (string) ($idpConfig['me_url'] ?? '');
                if ($meUrl === '') {
                    Log::warning('API auth callback(code): missing me URL configuration');
                    return redirect('/login')->with('error', 'Authentication failed: profile endpoint is not configured.');
                }
                $meResponse = $http->withToken($accessToken)->get($meUrl);
                $meData = $meResponse->json();
                if ($meResponse->failed()) {
                    Log::warning('API auth callback(code): profile fetch failed', [
                        'status' => $meResponse->status(),
                        'body' => $meData,
                    ]);
                    return redirect('/login')->with('error', 'Authentication failed: unable to fetch user profile.');
                }
                $email = $meData['email'] ?? null;
                if (!is_string($email) || $email === '') {
                    Log::warning('API auth callback(code): profile missing email', ['body' => $meData]);
                    return redirect('/login')->with('error', 'Authentication failed: profile email is missing.');
                }
                $rolesRaw = $meData['roles'] ?? [];
                if (is_array($rolesRaw)) {
                    $roles = $rolesRaw;
                } elseif (is_string($rolesRaw) && $rolesRaw !== '') {
                    $roles = [$rolesRaw];
                } else {
                    $roles = [];
                }
                $user = User::where('email', $email)->first();
                if (!$user) {
                    Log::warning('API auth callback(code): user not found, redirecting to onboarding', ['email' => $email]);
                    // Store IdP info in session for onboarding
                    Session::put('onboarding_idp', [
                        'first_name' => $meData['first_name'] ?? '',
                        'middle_name' => $meData['middle_name'] ?? '',
                        'last_name' => $meData['last_name'] ?? '',
                        'email' => $email,
                        'roles' => $roles,
                    ]);
                    return redirect()->route('onboarding.show', ['email' => $email]);
                }
                Log::info('API auth callback(code): role mapping', [
                    'idp_roles' => $roles,
                    'user_email' => $email,
                    'mapped_ojt_role' => $this->mapIdpRoleToOjtRole($roles, (string) ($user->role ?? '0')),
                    'user_current_role' => $user->role,
                ]);
                $ojtRole = $this->mapIdpRoleToOjtRole($roles, (string) ($user->role ?? '0'));
                if ((string) $user->role !== $ojtRole) {
                    $user->update(['role' => $ojtRole]);
                }
                // Set session for role middleware
                Session::put('loginId', $user->id);
                return $this->redirectByRole($ojtRole);
            } else {
                Log::warning('API auth callback(code): token exchange failed', [
                    'status' => $response->status(),
                    'body' => $data,
                ]);
                return redirect('/login')->with('error', 'Authentication failed: unable to exchange authorization code.');
            }
        } catch (\Exception $e) {
            Log::warning('API auth callback(code): token exchange exception', [
                'error' => $e->getMessage(),
            ]);
            return redirect('/login')->with('error', 'Authentication failed: unable to exchange authorization code.');
        }
    }

    /**
     * Core callback logic: decode JWT, find/update user, establish session, redirect.
     */
    private function handleCallback(Request $request, ?string $token)
    {
        if (empty($token)) {
            Log::warning('API auth callback: missing token', ['ip' => $request->ip()]);
            return redirect('/login')->with('error', 'Authentication failed: missing token.');
        }
        try {
            $payload = $this->jwt->decode($token);
        } catch (\RuntimeException $e) {
            Log::warning('API auth callback: JWT validation failed', [
                'reason' => $e->getMessage(),
                'ip'     => $request->ip(),
            ]);
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
        $userId = $payload->userId ?? null;
        $email = $payload->email ?? null;
        $idpRolesRaw = $payload->roles ?? [];
        if (is_array($idpRolesRaw)) {
            $idpRoles = $idpRolesRaw;
        } elseif (is_string($idpRolesRaw) && $idpRolesRaw !== '') {
            $idpRoles = [$idpRolesRaw];
        } else {
            $idpRoles = [];
        }
        if (!$userId || !$email) {
            Log::warning('API auth callback: missing userId or email in token', [
                'userId' => $userId,
                'email'  => $email,
                'ip'     => $request->ip(),
            ]);
            return redirect('/login')->with('error', 'Authentication failed: missing user information.');
        }
        $user = User::where('email', $email)->first();
        if (!$user) {
            Log::warning('API auth callback: user not found, redirecting to onboarding', [
                'email'  => $email,
                'userId' => $userId,
                'ip'     => $request->ip(),
            ]);
            // Store IdP info in session for onboarding
            Session::put('onboarding_idp', [
                'first_name' => $payload->first_name ?? '',
                'middle_name' => $payload->middle_name ?? '',
                'last_name' => $payload->last_name ?? '',
                'email' => $email,
                'roles' => $idpRoles,
            ]);
            return redirect()->route('onboarding.show');
        }
        $ojt_role = $this->mapIdpRoleToOjtRole($idpRoles, $user->role ?? '0');
        if ((string) $user->role !== $ojt_role) {
            $user->update(['role' => $ojt_role]);
        }
        // You may want to log the user in or set session here
        // Session::put('user_id', $user->id);
        // ...
        return $this->redirectByRole($ojt_role);
    }

    /**
     * Map IdP roles to OJT system role numbers.
     */
    private function mapIdpRoleToOjtRole(array $idpRoles, string $currentRole): string
    {
        $roleMap = [
            'student' => '0',
            'coordinator' => '1',
            'professor' => '2',
            '0' => '0',
            '1' => '1',
            '2' => '2',
        ];
        foreach ($idpRoles as $role) {
            $roleKey = strtolower((string) $role);
            // Remove OJTIMS: prefix if present
            if (str_starts_with($roleKey, 'ojtims:')) {
                $roleKey = substr($roleKey, strlen('ojtims:'));
            }
            if (isset($roleMap[$roleKey])) {
                return $roleMap[$roleKey];
            }
        }
        return $currentRole;
    }

    /**
     * Redirect user by OJT role.
     */
    private function redirectByRole(string $role)
    {
        switch ($role) {
            case '1': // coordinator
                return redirect('/dashboard');
            case '2': // professor
                return redirect('/professor/home');
            case '0': // student
            default:
                return redirect('/student/home');
        }
    }
}
