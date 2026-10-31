<?php

namespace App\Services;

use App\Mail\AnnouncementNotification;
use App\Models\Announcements;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Throwable;

class AnnouncementEmailService
{
    public function ready(): bool
    {
        return Schema::hasColumn('announcements', 'email_payload') && Schema::hasTable('announcement_email_deliveries');
    }

    public function mailer(): string
    {
        if (app()->environment('local')) {
            return 'announcement_log';
        }
        if (app()->environment('testing')) {
            return 'array';
        }

        return (string) config('announcement_email.mailer', 'smtp');
    }

    public function captureOnly(): bool
    {
        return in_array(config('mail.mailers.'.$this->mailer().'.transport'), ['log', 'array'], true);
    }

    public function options(): array
    {
        return ['available' => (bool) config('announcement_email.enabled') && $this->ready(), 'capture' => $this->captureOnly()];
    }

    public function plan(array $ids): array
    {
        if (!$this->options()['available']) {
            throw ValidationException::withMessages(['send_email' => 'Announcement email notifications are not enabled or their migration is missing. You can still publish without email.']);
        }
        $destinations = User::whereIn('id', $ids)->orderBy('id')->get(['id', 'role', 'email'])->map(function ($user) {
            $email = trim((string) $user->email);

            return ['user_id' => (int) $user->id, 'role' => (string) $user->role,
                'email' => strlen($email) <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null];
        })->all();
        $valid = count(array_filter($destinations, fn ($destination) => $destination['email'] !== null));
        if ($valid === 0) {
            throw ValidationException::withMessages(['send_email' => 'None of the selected recipients have a valid email address. Publish without email or correct their email records first.']);
        }

        return ['destinations' => $destinations, 'valid' => $valid, 'skipped' => count($destinations) - $valid, 'capture' => $this->captureOnly()];
    }

    public function enqueue(Announcements $announcement, array $plan): void
    {
        $savedIds = DB::table('announcement_recipients')->where('announcement_id', $announcement->id)->pluck('user_id')->map(fn ($id) => (int) $id)->all();
        $rows = [];
        foreach ($plan['destinations'] as $destination) {
            if (!in_array($destination['user_id'], $savedIds, true)) {
                throw new \LogicException('Email recipients must belong to the saved announcement audience.');
            }
            $rows[] = [
                'announcement_id' => $announcement->id, 'user_id' => $destination['user_id'],
                'email' => $destination['email'], 'recipient_role' => $destination['role'],
                'status' => $destination['email'] ? 'queued' : 'skipped',
                'last_error' => $destination['email'] ? null : 'missing_or_invalid_email',
                'created_at' => now(), 'updated_at' => now(),
            ];
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('announcement_email_deliveries')->insertOrIgnore($chunk);
        }
    }

    public function attachSummaries(Collection $announcements): void
    {
        if (!$this->ready() || $announcements->isEmpty()) {
            return;
        }
        $counts = DB::table('announcement_email_deliveries')->whereIn('announcement_id', $announcements->pluck('id'))
            ->select('announcement_id', 'status', DB::raw('COUNT(*) AS total'))->groupBy('announcement_id', 'status')->get()->groupBy('announcement_id');
        foreach ($announcements as $announcement) {
            $announcement->setAttribute('email_summary', $counts->get($announcement->id, collect())->pluck('total', 'status')->all());
        }
    }

    public function retryFailed(Announcements $announcement): int
    {
        return DB::table('announcement_email_deliveries')->where('announcement_id', $announcement->id)
            ->where('status', 'failed')->where('attempts', '<', 3)
            ->update(['status' => 'queued', 'processing_started_at' => null, 'last_error' => null, 'updated_at' => now()]);
    }

    public function markInterrupted(): int
    {
        return DB::table('announcement_email_deliveries')->where('status', 'sending')
            ->where('processing_started_at', '<', now()->subMinutes(15))
            ->update(['status' => 'uncertain', 'last_error' => 'worker_interrupted', 'updated_at' => now()]);
    }

    public function deliver(int $id): string
    {
        if (!config('announcement_email.enabled') || !$this->ready()) {
            return 'paused';
        }
        $claimed = DB::table('announcement_email_deliveries')->where('id', $id)->where('status', 'queued')
            ->update(['status' => 'sending', 'attempts' => DB::raw('attempts + 1'), 'processing_started_at' => now(), 'updated_at' => now()]);
        if (!$claimed) {
            return 'ignored';
        }
        $delivery = DB::table('announcement_email_deliveries')->find($id);
        $transportStarted = false;
        try {
            $announcement = Announcements::find($delivery->announcement_id);
            $recipientExists = DB::table('announcement_recipients')->where('announcement_id', $delivery->announcement_id)->where('user_id', $delivery->user_id)->exists();
            $user = User::find($delivery->user_id);
            $accountEligible = $user && ((string) $user->role === '0'
                ? (string) $user->status === '1'
                : ((string) $user->role === '2' && in_array((string) $user->status, AnnouncementAudienceService::PROFESSOR_STATUSES, true)));
            if (!$announcement || !$announcement->email_payload || !$recipientExists || !$user
                || !$accountEligible
                || (string) $user->role !== $delivery->recipient_role
                || !filter_var($delivery->email, FILTER_VALIDATE_EMAIL)) {
                $this->finish($id, 'skipped', 'recipient_unavailable');
                return 'skipped';
            }
            $payload = $announcement->email_payload;
            $baseUrl = $payload['base_url'];
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
            if (!parse_url($baseUrl, PHP_URL_HOST) || !in_array($scheme, ['http', 'https'], true)
                || (!app()->environment(['local', 'testing']) && $scheme !== 'https')) {
                throw new \RuntimeException('A secure application URL is required.');
            }
            $mailerName = $this->mailer();
            if (!in_array(config('mail.mailers.'.$mailerName.'.transport'), ['smtp', 'ses', 'mailgun', 'postmark', 'sendmail', 'log', 'array'], true)) {
                throw new \RuntimeException('Unsupported announcement mail transport.');
            }
            if (config('mail.mailers.'.$mailerName.'.transport') === 'smtp') {
                config()->set('mail.mailers.'.$mailerName.'.timeout', 30);
            }
            $mailer = Mail::mailer($mailerName);
            $path = $delivery->recipient_role === '2' ? '/professor/home' : '/student/home';
            $message = new AnnouncementNotification($payload, rtrim($baseUrl, '/').$path);
            $message->render();
            $transportStarted = true;
            $sent = $mailer->to($delivery->email)->send($message);
            if ($sent === null) {
                $this->finish($id, 'skipped', 'mail_suppressed');
                return 'skipped';
            }
            $status = $this->captureOnly() ? 'captured' : 'sent';
            $this->finish($id, $status);
            return $status;
        } catch (Throwable $exception) {
            $uncertain = $transportStarted;
            $status = $uncertain ? 'uncertain' : 'failed';
            $this->finish($id, $status, $uncertain ? 'transport_acceptance_unknown' : 'delivery_failed');
            Log::warning('Announcement email delivery did not complete.', ['delivery_id' => $id, 'status' => $status, 'exception_type' => get_class($exception)]);
            return $status;
        }
    }

    private function finish(int $id, string $status, ?string $error = null): void
    {
        DB::table('announcement_email_deliveries')->where('id', $id)->where('status', 'sending')->update([
            'status' => $status, 'last_error' => $error, 'updated_at' => now(),
            'sent_at' => in_array($status, ['sent', 'captured'], true) ? now() : null,
        ]);
    }
}
