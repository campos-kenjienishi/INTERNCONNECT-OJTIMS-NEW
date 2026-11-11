<?php

namespace App\Services;

use App\Mail\SupervisorEvaluationCode;
use App\Models\OjtEvaluationRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class SupervisorEmailVerification
{
    public function isVerified(OjtEvaluationRequest $invitation): bool
    {
        $grant = Session::get($this->grantKey($invitation));

        return is_array($grant)
            && ($grant['expires_at'] ?? 0) > now()->timestamp
            && ($grant['session_id'] ?? '') === Session::getId();
    }

    public function send(OjtEvaluationRequest $invitation, string $ip): void
    {
        $this->ensureEligible($invitation);
        $challengeKey = $this->challengeKey($invitation);
        $sent = Cache::lock($challengeKey . ':lock', 30)->get(function () use ($invitation, $ip, $challengeKey) {
            $this->limit('supervisor-code:send-ip:' . hash('sha256', $ip), 10, 600);
            $this->limit('supervisor-code:send-invitation:' . $this->signature($invitation), 3, 600);
            $this->limit($challengeKey . ':cooldown', 1, 60);

            $code = (string) random_int(100000, 999999);
            $expiresAt = min(now()->addMinutes(10)->timestamp, $invitation->token_expires_at->timestamp);
            Cache::put($challengeKey, ['hash' => Hash::make($code), 'expires_at' => $expiresAt], $expiresAt - now()->timestamp);

            try {
                Mail::to($invitation->supervisor_email)->send(new SupervisorEvaluationCode($code));
            } catch (\Throwable $exception) {
                Cache::forget($challengeKey);
                Log::warning('Supervisor verification email delivery failed.', ['request_id' => $invitation->id]);
                throw ValidationException::withMessages(['code' => 'The verification email could not be sent. Please try again later.']);
            }

            return true;
        });

        if (!$sent) {
            throw ValidationException::withMessages(['code' => 'A verification request is already in progress. Please wait.']);
        }
    }

    public function verify(OjtEvaluationRequest $invitation, string $code, string $ip): void
    {
        $this->ensureEligible($invitation);
        $challengeKey = $this->challengeKey($invitation);
        $verified = Cache::lock($challengeKey . ':lock', 30)->get(function () use ($invitation, $code, $ip, $challengeKey) {
            $this->limit('supervisor-code:verify-ip:' . hash('sha256', $ip), 30, 600);
            $this->limit($challengeKey . ':attempts', 5, 600);
            $challenge = Cache::get($challengeKey);

            if (!is_array($challenge) || ($challenge['expires_at'] ?? 0) <= now()->timestamp
                || !Hash::check($code, $challenge['hash'])) {
                throw ValidationException::withMessages(['code' => 'The code is incorrect or has expired. Request a new code if needed.']);
            }

            Cache::forget($challengeKey);
            Session::put($this->grantKey($invitation), [
                'expires_at' => min(now()->addMinutes(30)->timestamp, $invitation->token_expires_at->timestamp),
                'session_id' => Session::getId(),
            ]);

            return true;
        });

        if (!$verified) {
            throw ValidationException::withMessages(['code' => 'A verification request is already in progress. Please wait.']);
        }
    }

    public function forget(OjtEvaluationRequest $invitation): void
    {
        Session::forget($this->grantKey($invitation));
        Cache::forget($this->challengeKey($invitation));
    }

    private function ensureEligible(OjtEvaluationRequest $invitation): void
    {
        $current = OjtEvaluationRequest::whereKey($invitation->id)->where('token', $invitation->token)->first();
        if (!$current || !in_array($current->status, ['sent', 'opened'], true) || $current->tokenHasExpired()
            || $current->submitted_at || $current->evaluation()->exists()
            || $current->supervisor_email !== $invitation->supervisor_email) {
            throw ValidationException::withMessages(['code' => 'This evaluation link is no longer available. Please reload the page.']);
        }
    }

    private function limit(string $key, int $maximum, int $seconds): void
    {
        if (RateLimiter::tooManyAttempts($key, $maximum)) {
            throw ValidationException::withMessages(['code' => 'Too many requests or attempts. Please wait before trying again.']);
        }
        RateLimiter::hit($key, $seconds);
    }

    private function signature(OjtEvaluationRequest $invitation): string
    {
        return hash('sha256', $invitation->id . '|' . $invitation->token . '|' . $invitation->supervisor_email);
    }

    private function grantKey(OjtEvaluationRequest $invitation): string
    {
        return 'supervisor_verification.' . $this->signature($invitation);
    }

    private function challengeKey(OjtEvaluationRequest $invitation): string
    {
        return 'supervisor-code:' . $this->signature($invitation) . ':' . hash('sha256', Session::getId());
    }
}
