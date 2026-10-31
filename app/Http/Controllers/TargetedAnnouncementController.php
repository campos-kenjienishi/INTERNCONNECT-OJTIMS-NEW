<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Announcements;
use App\Models\User;
use App\Services\AnnouncementAudienceService;
use App\Services\AnnouncementEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TargetedAnnouncementController extends Controller
{
    public function __construct(private readonly AnnouncementAudienceService $audiences)
    {
    }

    private function coordinator(): User
    {
        $user = User::find(session('loginId'));
        abort_unless($user && (string) $user->role === '1', 403);
        abort_unless($this->audiences->ready(), 503, 'Announcement targeting is not installed. Please run the targeted announcement migration.');

        return $user;
    }

    public function professors(Request $request)
    {
        $this->coordinator();
        $filters = $this->audiences->normalize($request->validate($this->audiences->rules()));

        return response()->json(['professors' => $this->audiences->eligibleProfessors($filters)]);
    }

    public function preview(Request $request)
    {
        $user = $this->coordinator();
        $filters = $this->audiences->normalize($request->validate($this->audiences->rules()));
        $recipients = $this->audiences->resolve($filters);
        $nonce = Str::random(64);
        $emailPlan = $filters['send_email'] ? app(AnnouncementEmailService::class)->plan($recipients['ids']) : null;

        return response()->json([
            'students' => $recipients['students'],
            'professors' => $recipients['professors'],
            'total' => count($recipients['ids']),
            'publication_key' => $nonce,
            'email' => $emailPlan ? ['valid' => $emailPlan['valid'], 'skipped' => $emailPlan['skipped'], 'capture' => $emailPlan['capture']] : null,
            'confirmation' => $this->audiences->fingerprint($filters, array_merge($recipients['ids'], [$nonce, $emailPlan['destinations'] ?? []]), $user->id),
        ]);
    }

    public function publish(Request $request)
    {
        $user = $this->coordinator();
        $validated = $request->validate(array_merge($this->audiences->rules(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:20000'],
            'publication_key' => ['required', 'string', 'size:64', 'alpha_num'],
            'confirmation' => ['required', 'string', 'size:64'],
        ]));
        $existing = Announcements::where('publication_key', $validated['publication_key'])->first();
        if ($existing) {
            abort_unless((int) $existing->announcer_user_id === (int) $user->id, 403);
            return back()->with('success', 'This announcement has already been published.');
        }
        $filters = $this->audiences->normalize($validated);
        $recipients = $this->audiences->resolve($filters);
        $emailPlan = $filters['send_email'] ? app(AnnouncementEmailService::class)->plan($recipients['ids']) : null;
        $signature = $this->audiences->fingerprint($filters, array_merge($recipients['ids'], [$validated['publication_key'], $emailPlan['destinations'] ?? []]), $user->id);
        if (!hash_equals($signature, $validated['confirmation'])) {
            throw ValidationException::withMessages(['audience' => 'The recipient selection has changed. Preview the announcement again before publishing.']);
        }

        DB::transaction(function () use ($user, $validated, $filters, $recipients, $emailPlan) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if (Announcements::where('publication_key', $validated['publication_key'])->exists()) {
                return;
            }
            $announcement = new Announcements();
            $announcement->announcer = $user->full_name;
            $announcement->announcer_user_id = $user->id;
            $announcement->title = $validated['title'];
            $announcement->content = $validated['content'];
            $announcement->audience = 'targeted';
            $announcement->targeting = array_merge($filters, [
                'student_count' => $recipients['students']->count(),
                'professor_count' => $recipients['professors']->count(),
            ]);
            $announcement->publication_key = $validated['publication_key'];
            if ($emailPlan) {
                $announcement->email_payload = [
                    'title' => $validated['title'], 'content' => $validated['content'],
                    'coordinator' => $user->full_name, 'base_url' => rtrim((string) config('app.url'), '/'),
                ];
            }
            $announcement->save();
            foreach (array_chunk($recipients['ids'], 500) as $ids) {
                DB::table('announcement_recipients')->insert(array_map(fn ($id) => [
                    'announcement_id' => $announcement->id, 'user_id' => $id,
                ], $ids));
            }
            if ($emailPlan) {
                app(AnnouncementEmailService::class)->enqueue($announcement, $emailPlan);
            }
            AuditLogger::log('Announcements', 'Create', 'Published targeted announcement #'.$announcement->id.': '.$announcement->title.' to '.count($recipients['ids']).' recipients ('.$filters['audience'].').', $user->id);
        });

        return back()->with('success', $emailPlan
            ? 'Announcement published. Email notifications are queued for background processing.'
            : 'Announcement published to the selected recipients.');
    }

    public function retryEmails(int $id)
    {
        $user = $this->coordinator();
        $announcement = Announcements::whereKey($id)->where('announcer_user_id', $user->id)->firstOrFail();
        $emails = app(AnnouncementEmailService::class);
        abort_unless($emails->ready() && config('announcement_email.enabled'), 503, 'Announcement email is unavailable.');
        $count = DB::transaction(function () use ($emails, $announcement, $user) {
            $count = $emails->retryFailed($announcement);
            AuditLogger::log('Announcements', 'Retry email', 'Requeued '.$count.' failed email notifications for announcement #'.$announcement->id.'.', $user->id);
            return $count;
        });

        return back()->with('success', $count.' failed email notification(s) queued. Sent, captured, and uncertain deliveries were not retried.');
    }

    public function designate(Request $request)
    {
        $user = $this->coordinator();
        $validated = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 2)->whereIn('status', AnnouncementAudienceService::PROFESSOR_STATUSES))],
            'course' => ['required', 'string', Rule::exists('courses', 'course')],
            'school_year' => ['required', 'regex:/^20\d{2}-20\d{2}$/'],
            'semester' => ['nullable', Rule::in(AnnouncementAudienceService::SEMESTERS)],
        ]);
        [$start, $end] = explode('-', $validated['school_year']);
        if ((int) $end !== (int) $start + 1) {
            throw ValidationException::withMessages(['school_year' => 'School year must contain consecutive years.']);
        }
        DB::transaction(function () use ($validated, $user) {
            DB::table('ojt_professor_designations')->updateOrInsert([
                'user_id' => $validated['user_id'], 'course' => $validated['course'],
                'school_year' => $validated['school_year'], 'semester' => $validated['semester'] ?? '',
            ], ['designated_by' => $user->id, 'created_at' => now(), 'updated_at' => now()]);
            AuditLogger::log('Announcements', 'Designate', 'Designated OJT professor for '.$validated['course'].' ('.$validated['school_year'].').', $validated['user_id']);
        });

        return back()->with('success', 'OJT professor designation saved. Existing announcement recipients remain unchanged.');
    }

    public function removeDesignation(int $id)
    {
        $this->coordinator();
        $designation = DB::table('ojt_professor_designations')->find($id);
        abort_unless($designation, 404);
        DB::transaction(function () use ($designation, $id) {
            DB::table('ojt_professor_designations')->where('id', $id)->delete();
            AuditLogger::log('Announcements', 'Remove designation', 'Removed OJT professor designation #'.$id.' for '.$designation->course.' ('.$designation->school_year.').', $designation->user_id);
        });

        return back()->with('success', 'Designation removed. Existing announcement recipients remain unchanged.');
    }
}
