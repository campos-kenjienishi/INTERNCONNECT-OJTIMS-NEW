<?php

namespace App\Services;

use App\Helpers\AuditLogger;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DataRetentionService
{
    public const CATEGORIES = [
        'requirements' => 'Regular requirement PDFs',
        'completion_records' => 'COCs and supervisor evaluations',
        'student_profile' => 'Student account, profile and OJT information',
        'moa' => 'Expired MOA and renewal history',
    ];

    public function ready(): bool
    {
        return Schema::hasTable('retention_states') && Schema::hasTable('retention_disposals')
            && Schema::hasTable('retention_file_deletions');
    }

    public function preview(string $category, int $id): array
    {
        abort_unless(isset(self::CATEGORIES[$category]), 404);
        $type = $category === 'moa' ? 'moa' : 'student';
        $state = DB::table('retention_states')->where('subject_type', $type)->where('subject_id', $id)->first();
        $subject = DB::table($type === 'moa' ? 'companies' : 'users')->where('id', $id)->first();
        abort_unless($subject && ($type === 'moa' || (string) $subject->role === '0'), 404);
        $plan = ['category' => $category, 'subject_id' => $id, 'label' => $type === 'moa' ? $subject->company_name : $subject->full_name,
            'eligible' => false, 'reason' => '', 'due_on' => null, 'records' => [], 'files' => []];
        $blocked = null;
        if ($state?->hold_until && CarbonImmutable::parse($state->hold_until)->endOfDay()->gte(now())) {
            $blocked = 'Temporary hold is active until '.$state->hold_until.'.';
        }

        if ($type === 'student') {
            $student = DB::table('students')->where('user_id', $id)->first();
            abort_unless($student, 404);
            $requirements = $this->ownedRequirements($subject);
            $legacy = $requirements->contains(fn ($row) => empty($row->uploader_user_id));
            if ($legacy && DB::table('users')->where('full_name', $subject->full_name)->count() !== 1) {
                $blocked = 'Ambiguous legacy document ownership. Resolve duplicate names before disposal.';
            }
            $certificates = $this->rows('completion_certificates', 'student_user_id', $id);
            $requests = $this->rows('ojt_evaluation_requests', 'student_id', $id);
            $evaluations = $this->rows('ojt_evaluations', 'request_id', $requests->pluck('id')->all());
            $special = fn ($row) => in_array(mb_strtolower(trim($row->fileName)), ['notarized moa', 'certificate of completion', 'completion certificate'], true);
            if ($category === 'requirements') {
                $this->addRows($plan, 'file_requirements', $requirements->reject($special), 'requirements', 'file');
            } elseif ($category === 'completion_records') {
                if ($requests->contains(fn ($row) => ! in_array($row->status, ['submitted', 'cancelled', 'expired'], true))) {
                    $blocked = 'A supervisor evaluation is still in progress.';
                }
                if ($certificates->contains(fn ($row) => empty($row->superseded_at) && $row->status !== 'approved')) {
                    $blocked = 'A current COC still needs final approval or correction.';
                }
                $this->addRows($plan, 'file_requirements', $requirements->filter(fn ($row) => in_array(mb_strtolower(trim($row->fileName)), ['certificate of completion', 'completion certificate'], true)), 'requirements', 'file');
                foreach ($certificates as $certificate) {
                    $this->addRows($plan, 'completion_certificates', collect([$certificate]), $certificate->storage_directory, 'file');
                }
                $this->addRows($plan, 'ojt_evaluations', $evaluations);
                $this->addRows($plan, 'ojt_evaluation_requests', $requests);
            } else {
                if ($requirements->isNotEmpty() || $certificates->isNotEmpty() || $requests->isNotEmpty()) {
                    $blocked = 'Dispose eligible requirements and completion records first. Retained MOA submissions must be resolved separately.';
                }
                if ($this->rows('company_student', 'student_id', $student->id)->isNotEmpty()) {
                    $blocked = 'Student still has company placement links. Resolve the placement links before profile disposal.';
                }
                if (DB::table('companies')->where('uploader_name', $subject->full_name)->exists()) {
                    $blocked = 'Student is the named MOA owner. Transfer ownership or dispose the eligible MOA first.';
                }
                if (Schema::hasTable('vouchers') && DB::table('vouchers')->where('uploader_name', $subject->full_name)->exists()) {
                    $blocked = 'A retained voucher still identifies this student. Resolve the associated MOA first.';
                }
                if (Schema::hasColumn('companies', 'student_names_display') && DB::table('companies')->where('student_names_display', 'like', '%'.$subject->full_name.'%')->exists()) {
                    $blocked = 'A retained MOA still contains this student in its manual student list. Update that list first.';
                }
                foreach (['professors' => 'user_id', 'ojt_professor_designations' => 'user_id', 'uploaded_files' => 'uploader_user_id', 'announcements' => 'announcer_user_id'] as $table => $column) {
                    if (Schema::hasTable($table) && Schema::hasColumn($table, $column) && DB::table($table)->where($column, $id)->exists()) {
                        $blocked = 'This account owns faculty or published resources. Resolve those references before profile disposal.';
                    }
                }
                $number = trim((string) ($student->studentNum ?? ''));
                if ($number !== '' && DB::table('students')->where('studentNum', $number)->where('id', '!=', $student->id)->exists()) {
                    $blocked = 'Student number is shared by multiple profiles. Resolve the ambiguity first.';
                }
                if (Schema::hasTable('announcement_email_deliveries') && DB::table('announcement_email_deliveries')->where('user_id', $id)->where('status', 'sending')->exists()) {
                    $blocked = 'An email delivery is in progress. Retry after it finishes.';
                }
                if ($number !== '') {
                    $this->addRows($plan, 'o_j_t_information', $this->rows('o_j_t_information', 'studentNum', $number));
                }
                foreach (['announcement_email_deliveries', 'announcement_recipients', 'sessions'] as $table) {
                    if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                        $this->addRows($plan, $table, $this->rows($table, 'user_id', $id));
                    }
                }
                $this->addRows($plan, 'moa_unlock_requests', $this->rows('moa_unlock_requests', 'student_id', $student->id));
                $this->addRows($plan, 'password_reset_tokens', $this->rows('password_reset_tokens', 'email', $subject->email), null, null, 'email');
                if (Schema::hasTable('personal_access_tokens')) {
                    $this->addRows($plan, 'personal_access_tokens', DB::table('personal_access_tokens')->where('tokenable_type', \App\Models\User::class)->where('tokenable_id', $id)->get());
                }
                foreach ([$subject->profile_photo ?? null, $student->profile_photo ?? null] as $photo) {
                    if ($photo) {
                        $plan['files'][] = ['disk' => 'public', 'path' => $photo];
                    }
                }
                $this->addRows($plan, 'students', collect([$student]));
                $this->addRows($plan, 'users', collect([$subject]));
            }
            if (! $state?->completed_on || ! $state?->clearance_finalized_on) {
                $blocked ??= 'Confirmed completion and clearance-finalization dates are required. Account age is never used.';
            } else {
                $anchor = max($state->completed_on, $state->clearance_finalized_on);
                $key = match ($category) {
                    'requirements' => 'requirements_years',
                    'completion_records' => 'completion_records_years',
                    default => 'profiles_years',
                };
                $plan['due_on'] = $this->due($anchor, $key);
            }
        } else {
            $expiry = app(MoaValidityService::class)->parseDate($subject->valid_until);
            if (! $expiry) {
                $blocked ??= 'A valid MOA expiration date is required.';
            } else {
                $plan['due_on'] = $this->due($expiry->toDateString(), 'moa_years');
            }
            $links = $this->rows('company_student', 'company_id', $id);
            if ($this->rows('moa_unlock_requests', 'company_id', $id)->contains(fn ($row) => $row->status === 'pending')) {
                $blocked = 'A MOA unlock request is awaiting review.';
            }
            foreach ($links as $link) {
                $student = DB::table('students')->where('id', $link->student_id)->first();
                if (! $student || ! $this->studentFinished((int) $student->user_id)) {
                    $blocked = 'An active or unconfirmed student placement still references this MOA.';
                }
            }
            $renewals = $this->rows('moa_renewals', 'company_id', $id);
            $filenames = $renewals->pluck('previous_file')->merge($renewals->pluck('new_file'))->push($subject->file)->filter()->unique();
            $linkedRequirements = Schema::hasTable('file_requirements')
                ? DB::table('file_requirements')->whereIn('file', $filenames)->get() : collect();
            foreach ($linkedRequirements as $requirement) {
                if (mb_strtolower(trim($requirement->fileName)) !== 'notarized moa') {
                    continue;
                }
                $owners = empty($requirement->uploader_user_id)
                    ? DB::table('users')->where('full_name', $requirement->uploadedBy)->get()
                    : DB::table('users')->where('id', $requirement->uploader_user_id)->get();
                if ($owners->count() !== 1 || ! $this->studentFinished((int) $owners->first()->id)) {
                    $blocked = 'A submitted MOA still belongs to an active or unresolved student.';
                }
            }
            $exclusiveRequirements = $linkedRequirements->filter(function ($requirement) use ($id) {
                return mb_strtolower(trim($requirement->fileName)) === 'notarized moa'
                    && ! DB::table('companies')->where('id', '!=', $id)->where('file', $requirement->file)->exists()
                    && ! DB::table('moa_renewals')->where('company_id', '!=', $id)->where(function ($query) use ($requirement) {
                        $query->where('previous_file', $requirement->file)->orWhere('new_file', $requirement->file);
                    })->exists();
            });
            $this->addRows($plan, 'file_requirements', $exclusiveRequirements, 'moa', 'file');
            $this->addRows($plan, 'moa_unlock_requests', $this->rows('moa_unlock_requests', 'company_id', $id));
            $this->addRows($plan, 'vouchers', $this->rows('vouchers', 'company_id', $id), 'moa', 'filename');
            $this->addRows($plan, 'moa_renewals', $renewals);
            foreach ($filenames as $filename) {
                $plan['files'][] = ['disk' => config('upload_security.disk', 'secure_uploads'), 'path' => 'moa/'.$filename];
                $plan['files'][] = ['disk' => 'legacy', 'path' => $filename];
            }
            $this->addRows($plan, 'company_student', $links, null, null, null);
            $this->addRows($plan, 'companies', collect([$subject]));
        }

        $plan['files'] = array_values(array_unique($plan['files'], SORT_REGULAR));
        usort($plan['files'], fn ($first, $second) => strcmp($first['disk'].'/'.$first['path'], $second['disk'].'/'.$second['path']));
        $plan['record_count'] = array_sum(array_map(fn ($group) => count($group['rows']), $plan['records']));
        $plan['reason'] = $blocked ?? ($plan['due_on'] > now()->toDateString() ? 'Retention period has not ended.' : ($plan['record_count'] ? 'Retention period has ended; ready for explicit disposal.' : 'No matching records remain.'));
        $plan['eligible'] = ! $blocked && $plan['due_on'] && $plan['due_on'] <= now()->toDateString() && $plan['record_count'] > 0;
        $plan['fingerprint'] = hash('sha256', json_encode([$subject, $state, $plan, config('retention')], JSON_THROW_ON_ERROR));

        return $plan;
    }

    public function dispose(string $category, int $id, string $fingerprint, int $actor): int
    {
        if (! config('retention.disposal_enabled')) {
            throw ValidationException::withMessages(['retention' => 'Preview-only mode is enabled. No records or files were deleted.']);
        }
        $lock = Cache::lock('retention:disposal', 300);
        if (! $lock->get()) {
            throw ValidationException::withMessages(['retention' => 'Another disposal is running. Please wait.']);
        }
        try {
            $disposal = DB::transaction(function () use ($category, $id, $fingerprint, $actor) {
                DB::table('retention_states')->where('subject_type', $category === 'moa' ? 'moa' : 'student')->where('subject_id', $id)->lockForUpdate()->get();
                DB::table($category === 'moa' ? 'companies' : 'users')->where('id', $id)->lockForUpdate()->first();
                $plan = $this->preview($category, $id);
                if (! $plan['eligible'] || ! hash_equals($plan['fingerprint'], $fingerprint)) {
                    throw ValidationException::withMessages(['retention' => 'The preview changed or disposal is no longer allowed. Generate a new preview.']);
                }
                $disposal = DB::table('retention_disposals')->insertGetId([
                    'category' => $category, 'subject_id' => $id, 'executed_by' => $actor,
                    'records_removed' => $plan['record_count'], 'status' => 'files_pending', 'created_at' => now(), 'updated_at' => now(),
                ]);
                foreach ($plan['files'] as $file) {
                    app(RetentionFileCleaner::class)->validatePath($file['disk'], $file['path']);
                    DB::table('retention_file_deletions')->insert($file + ['disposal_id' => $disposal, 'created_at' => now(), 'updated_at' => now()]);
                }
                foreach ($plan['records'] as $group) {
                    if ($group['key'] !== null) {
                        DB::table($group['table'])->whereIn($group['key'], array_column($group['rows'], $group['key']))->delete();
                    } else {
                        DB::table('company_student')->where('company_id', $id)->delete();
                    }
                }
                if (in_array($category, ['student_profile', 'moa'], true)) {
                    DB::table('retention_states')->where('subject_type', $category === 'moa' ? 'moa' : 'student')->where('subject_id', $id)->delete();
                }
                AuditLogger::log('Data Retention', 'dispose', 'Disposal #'.$disposal.' executed for '.$category.' subject #'.$id.'; '.$plan['record_count'].' records removed. File cleanup is tracked separately.');

                return $disposal;
            });
            if ($category === 'student_profile') {
                Cache::forget('active_session_id:'.$id);
            }
            app(RetentionFileCleaner::class)->process($disposal);

            return $disposal;
        } finally {
            $lock->release();
        }
    }

    public function studentFinished(int $id): bool
    {
        $state = DB::table('retention_states')->where('subject_type', 'student')->where('subject_id', $id)->first();

        return $state && $state->completed_on && $state->clearance_finalized_on
            && (! $state->hold_until || $state->hold_until < now()->toDateString())
            && $state->completed_on <= now()->toDateString() && $state->clearance_finalized_on <= now()->toDateString();
    }

    private function due(string $anchor, string $key): string
    {
        $years = (int) config('retention.'.$key);
        if ($years < 1 || $years > 20) {
            throw ValidationException::withMessages(['retention' => 'Retention periods must be between 1 and 20 years.']);
        }

        return CarbonImmutable::parse($anchor)->addYearsNoOverflow($years)->toDateString();
    }

    private function ownedRequirements(object $user): Collection
    {
        return DB::table('file_requirements')->where(function ($query) use ($user) {
            $query->where('uploader_user_id', $user->id)->orWhere(function ($legacy) use ($user) {
                $legacy->whereNull('uploader_user_id')->where('uploadedBy', $user->full_name);
            });
        })->orderBy('id')->get();
    }

    private function rows(string $table, string $column, mixed $value): Collection
    {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $query = DB::table($table)->whereIn($column, is_array($value) ? $value : [$value]);
        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    private function addRows(array &$plan, string $table, Collection $rows, ?string $directory = null, ?string $fileColumn = null, ?string $key = 'id'): void
    {
        if ($rows->isEmpty()) {
            return;
        }
        $plan['records'][] = ['table' => $table, 'key' => $key, 'rows' => $rows->sortBy(fn ($row) => $key === null ? json_encode($row) : (string) $row->{$key})->map(fn ($row) => (array) $row)->values()->all()];
        if ($directory !== null && $fileColumn !== null) {
            foreach ($rows as $row) {
                if (! empty($row->{$fileColumn})) {
                    $plan['files'][] = ['disk' => config('upload_security.disk', 'secure_uploads'), 'path' => $directory.'/'.$row->{$fileColumn}];
                    $plan['files'][] = ['disk' => 'legacy', 'path' => $row->{$fileColumn}];
                }
            }
        }
    }
}
