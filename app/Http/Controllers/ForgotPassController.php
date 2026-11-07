<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Mail\ForgotPassNotif;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ForgotPassController extends Controller
{
    private const INVALID_LINK = 'This local password reset link is invalid or expired. Please request a new link.';

    public function resetP(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');
        $user = is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $email)->first() : null;
        if (! $user || ! $user->has_local_password || ! is_string($token)
            || preg_match('/^[a-f0-9]{64}$/', $token) !== 1
            || ! Password::broker()->tokenExists($user, $token)) {
            return redirect('/forgot')->with('fail', self::INVALID_LINK)->withHeaders($this->privateHeaders());
        }

        return response()->view('auth.reset', ['email' => $user->email, 'token' => $token])
            ->withHeaders($this->privateHeaders());
    }

    public function forgotP()
    {
        return view('auth.forgot');
    }

    public function forgotPass(Request $request)
    {
        $values = $request->validate(['email' => 'required|string|email|max:254']);
        try {
            DB::transaction(function () use ($values) {
                $user = User::where('email', $values['email'])->lockForUpdate()->first();
                if (! $user || ! $user->has_local_password) {
                    return;
                }
                Password::broker()->sendResetLink(['email' => $user->email], function (User $recipient, string $token) {
                    $baseUrl = rtrim((string) config('app.url'), '/');
                    $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
                    if (! filter_var($baseUrl, FILTER_VALIDATE_URL) || ! in_array($scheme, ['http', 'https'], true)
                        || (! app()->environment(['local', 'testing']) && $scheme !== 'https')) {
                        throw new RuntimeException('A valid HTTPS application URL is required for password recovery.');
                    }
                    $url = $baseUrl.'/reset?'.http_build_query(['email' => $recipient->email, 'token' => $token], '', '&', PHP_QUERY_RFC3986);
                    Mail::to($recipient->email)->send(new ForgotPassNotif($url, (int) config('auth.passwords.users.expire', 60)));
                });
            });
        } catch (Throwable $exception) {
            Log::warning('Local password recovery request could not be processed.', ['exception_type' => $exception::class]);
        }

        return redirect('/forgot')->with('success', 'If this email belongs to an account with local sign-in enabled, a reset link will be sent. For institutional credentials, use the IdP recovery process.');
    }

    public function resetPass(Request $request)
    {
        $values = $request->validate([
            'email' => 'required|string|email|max:254',
            'token' => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]{64}$/'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/'],
            'confirm_password' => 'required|same:password',
        ], [
            'password.regex' => 'Use uppercase and lowercase letters, a number, and one of these symbols: ! @ # $ % ^ & *.',
            'confirm_password.same' => 'Password confirmation does not match.',
        ]);

        $status = DB::transaction(function () use ($values) {
            $user = User::where('email', $values['email'])->lockForUpdate()->first();
            if (! $user || ! $user->has_local_password) {
                return Password::INVALID_TOKEN;
            }
            DB::table(config('auth.passwords.users.table', 'password_reset_tokens'))->where('email', $user->email)->lockForUpdate()->first();

            return Password::broker()->reset([
                'email' => $user->email, 'token' => $values['token'],
                'password' => $values['password'], 'password_confirmation' => $values['confirm_password'],
            ], function (User $target, string $password) {
                if (Hash::check($password, $target->password)) {
                    throw ValidationException::withMessages(['password' => 'Your new local password must be different from your current password.']);
                }
                $target->password = Hash::make($password);
                if (Schema::hasColumn('users', 'remember_token')) {
                    $target->remember_token = Str::random(60);
                }
                $target->save();
                if (Schema::hasTable('personal_access_tokens')) {
                    $target->tokens()->delete();
                }
                Cache::forget('active_session_id:'.$target->id);
                AuditLogger::log('Authentication', 'password reset', 'Local password reset completed through a verified email reset link. Institutional IdP credentials were not changed.', $target->id);
            });
        });

        if ($status !== Password::PASSWORD_RESET) {
            return redirect('/forgot')->with('fail', self::INVALID_LINK)->withHeaders($this->privateHeaders());
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.gateway')->with('success', 'Your local password has been reset. Please sign in again. Your institutional IdP password has not changed.')
            ->withHeaders($this->privateHeaders());
    }

    private function privateHeaders(): array
    {
        return [
            'Cache-Control' => 'no-store, private', 'Referrer-Policy' => 'no-referrer', 'X-Robots-Tag' => 'noindex, nofollow',
            'X-Frame-Options' => 'DENY',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'; object-src 'none'",
        ];
    }
}
