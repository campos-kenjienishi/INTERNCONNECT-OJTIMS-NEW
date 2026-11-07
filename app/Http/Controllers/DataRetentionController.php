<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\User;
use App\Services\DataRetentionService;
use App\Services\RetentionFileCleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class DataRetentionController extends Controller
{
    public function workspace(Request $request, DataRetentionService $retention): array
    {
        if (! $retention->ready()) {
            return ['ready' => false];
        }
        $kind = $request->query('retention_kind') === 'moa' ? 'moa' : 'student';
        $search = mb_substr(trim((string) $request->query('retention_search', '')), 0, 100);
        $query = $kind === 'student'
            ? DB::table('users')->join('students', 'students.user_id', '=', 'users.id')
                ->where('users.role', 0)->select(['users.id', 'users.full_name as label', 'students.course', 'users.last_activity_at', 'users.created_at'])
            : DB::table('companies')->select(['id', 'company_name as label', 'valid_until']);
        if ($search !== '') {
            $query->where($kind === 'student' ? 'users.full_name' : 'company_name', 'like', '%'.$search.'%');
        }
        $subjects = $query->orderBy($kind === 'student' ? 'users.id' : 'id')->paginate(10, ['*'], 'retention_page')->withQueryString()->fragment('data-retention');
        $states = DB::table('retention_states')->where('subject_type', $kind)->whereIn('subject_id', $subjects->pluck('id'))->get()->keyBy('subject_id');

        return [
            'ready' => true, 'kind' => $kind, 'search' => $search, 'subjects' => $subjects, 'states' => $states,
            'disposals' => DB::table('retention_disposals')->orderByDesc('id')->paginate(10, ['*'], 'disposal_page')->withQueryString()->fragment('data-retention'),
        ];
    }

    public function update(Request $request, DataRetentionService $retention)
    {
        $user = $this->coordinator();
        abort_unless($retention->ready(), 503, 'Run the data-retention migration first.');
        $values = $request->validate([
            'subject_type' => ['required', Rule::in(['student', 'moa'])],
            'subject_id' => 'required|integer|min:1',
            'completed_on' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'clearance_finalized_on' => 'nullable|date_format:Y-m-d|required_with:completed_on|after_or_equal:completed_on|before_or_equal:today',
            'completion_confirmed' => 'accepted_if:subject_type,student',
            'hold_until' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'hold_reason' => 'nullable|string|max:500|required_with:hold_until',
        ]);
        if (! empty($values['clearance_finalized_on']) && empty($values['completed_on'])) {
            throw ValidationException::withMessages(['completed_on' => 'Enter completion and clearance dates together.']);
        }
        if ($values['subject_type'] === 'student') {
            abort_unless(DB::table('users')->where('id', $values['subject_id'])->where('role', 0)->exists()
                && DB::table('students')->where('user_id', $values['subject_id'])->exists(), 404);
        } else {
            abort_unless(DB::table('companies')->where('id', $values['subject_id'])->exists(), 404);
        }
        $lock = Cache::lock('retention:disposal', 300);
        abort_unless($lock->get(), 409, 'A disposal is in progress. Try again shortly.');
        try {
            DB::transaction(function () use ($values, $user) {
                DB::table('retention_states')->updateOrInsert(
                    ['subject_type' => $values['subject_type'], 'subject_id' => $values['subject_id']],
                    [
                        'completed_on' => $values['subject_type'] === 'student' ? ($values['completed_on'] ?? null) : null,
                        'clearance_finalized_on' => $values['subject_type'] === 'student' ? ($values['clearance_finalized_on'] ?? null) : null,
                        'hold_until' => $values['hold_until'] ?? null,
                        'hold_reason' => empty($values['hold_until']) ? null : $values['hold_reason'],
                        'updated_by' => $user->id, 'created_at' => now(), 'updated_at' => now(),
                    ]
                );
                AuditLogger::log('Data Retention', 'update', 'Updated retention dates/hold for '.$values['subject_type'].' record #'.$values['subject_id']);
            });
        } finally {
            $lock->release();
        }

        return back()->with('success', 'Retention dates and temporary hold saved. No files or student records were deleted.');
    }

    public function preview(Request $request, DataRetentionService $retention)
    {
        $user = $this->coordinator();
        abort_unless($retention->ready(), 503);
        $values = $request->validate(['category' => ['required', Rule::in(array_keys(DataRetentionService::CATEGORIES))], 'subject_id' => 'required|integer|min:1']);
        $plan = $retention->preview($values['category'], $values['subject_id']);
        $token = Crypt::encryptString(json_encode([
            'category' => $values['category'], 'subject_id' => (int) $values['subject_id'],
            'fingerprint' => $plan['fingerprint'], 'actor' => $user->id, 'expires' => now()->addMinutes(5)->timestamp,
        ], JSON_THROW_ON_ERROR));

        return response()->json([
            'label' => $plan['label'], 'eligible' => $plan['eligible'], 'reason' => $plan['reason'], 'due_on' => $plan['due_on'],
            'record_count' => $plan['record_count'], 'file_count' => count($plan['files']),
            'tables' => collect($plan['records'])->groupBy('table')->map(fn ($groups) => $groups->sum(fn ($group) => count($group['rows']))),
            'files' => array_slice($plan['files'], 0, 100),
            'disposal_enabled' => (bool) config('retention.disposal_enabled'), 'token' => $token,
        ]);
    }

    public function dispose(Request $request, DataRetentionService $retention)
    {
        $user = $this->coordinator();
        $values = $request->validate(['token' => 'required|string', 'confirmation' => ['required', Rule::in(['DISPOSE'])]]);
        try {
            $preview = json_decode(Crypt::decryptString($values['token']), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['token' => 'Invalid preview. Generate a new preview.']);
        }
        if (($preview['actor'] ?? null) !== $user->id || ($preview['expires'] ?? 0) <= now()->timestamp) {
            throw ValidationException::withMessages(['token' => 'Preview expired or belongs to another coordinator. Generate a new preview.']);
        }
        $id = $retention->dispose($preview['category'], $preview['subject_id'], $preview['fingerprint'], $user->id);
        $status = DB::table('retention_disposals')->where('id', $id)->value('status');

        return response()->json(['message' => $status === 'completed'
            ? 'Disposal completed. Files still referenced elsewhere were preserved.'
            : 'Database disposal recorded, but some file cleanup is pending. Use Retry file cleanup; do not treat this as complete.', 'status' => $status]);
    }

    public function retry(int $disposal, RetentionFileCleaner $cleaner)
    {
        $this->coordinator();
        abort_unless(config('retention.disposal_enabled'), 403, 'Preview-only mode is enabled.');
        abort_unless(DB::table('retention_disposals')->where('id', $disposal)->exists(), 404);
        $lock = Cache::lock('retention:disposal', 300);
        abort_unless($lock->get(), 409);
        try {
            $cleaner->process($disposal);
        } finally {
            $lock->release();
        }

        return back()->with('success', 'File cleanup retried. Check the disposal status below.');
    }

    private function coordinator(): User
    {
        $user = User::find(session('loginId'));
        abort_unless($user && (string) $user->role === '1', 403);

        return $user;
    }
}
