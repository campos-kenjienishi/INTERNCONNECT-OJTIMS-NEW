<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use App\Services\SecureUploadService;
use App\Services\UploadSecurityRecorder;
use Carbon\CarbonImmutable;
use Dompdf\Dompdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SeedRetentionDemo extends Command
{
    protected $signature = 'retention:seed-demo {--confirm : Create synthetic records in the local database}';

    protected $description = 'Create clearly marked local-only retention scenarios without sending mail or enabling disposal';

    private array $createdFiles = [];

    public function handle(DataRetentionService $retention): int
    {
        $this->createdFiles = [];
        $connection = config('database.connections.'.config('database.default'));
        $localDatabase = ($connection['driver'] ?? null) === 'sqlite'
            || (($connection['driver'] ?? null) === 'mysql' && in_array($connection['host'] ?? '', ['127.0.0.1', 'localhost', '::1'], true));
        $localApplication = app()->environment('testing') || in_array(parse_url((string) config('app.url'), PHP_URL_HOST), ['localhost', '127.0.0.1', '::1', '[::1]'], true);
        if (! app()->environment(['local', 'testing']) || ! $localDatabase || ! $localApplication) {
            $this->error('Demo seeding is restricted to local/testing environments and a local database.');

            return self::FAILURE;
        }
        if (! $this->option('confirm') || ! $retention->ready()) {
            $this->error('Run the retention migration first, then use retention:seed-demo --confirm.');

            return self::FAILURE;
        }
        $lock = Cache::lock('retention:disposal', 300);
        if (! $lock->get()) {
            $this->error('Another retention operation is in progress. Try again shortly.');

            return self::FAILURE;
        }
        try {
            $report = DB::transaction(fn () => $this->seed());
        } catch (Throwable $exception) {
            foreach ($this->createdFiles as $path) {
                Storage::disk(config('upload_security.disk'))->delete($path);
            }
            $this->error('Demo creation rolled back: '.$exception->getMessage());

            return self::FAILURE;
        } finally {
            $lock->release();
        }
        $this->table(['Type', 'ID', 'Label', 'Expected scenario', 'Result'], $report);
        $this->info('Search RETENTION DEMO in Coordinator > Maintenance > Data Retention & Disposal.');
        $this->line('Existing scenarios were not overwritten. No email was sent and disposal configuration was not changed.');

        return self::SUCCESS;
    }

    private function seed(): array
    {
        $report = [];
        $students = [];
        $old = CarbonImmutable::today()->subYearsNoOverflow(max(1, (int) config('retention.requirements_years'), (int) config('retention.completion_records_years'), (int) config('retention.profiles_years'), (int) config('retention.moa_years')) + 1)->subMonthNoOverflow()->toDateString();
        $recent = CarbonImmutable::today()->subDays(15)->toDateString();
        $scenarios = [
            'due' => ['01 Due Student', 'Requirements and completion records eligible; profile blocked until those are disposed'],
            'recent' => ['02 Recent Student', 'Retention period not ended'],
            'held' => ['03 Held Student', 'Temporary hold blocks disposal'],
            'incomplete' => ['04 Incomplete Student', 'Old account but no completion/clearance; review only'],
            'shared-owner' => ['05 Shared Owner', 'Requirement eligible, shared physical file must remain'],
            'active-reader' => ['06 Active Shared Reader', 'Unconfirmed student still references the shared file'],
            'profile-only' => ['07 Profile Only', 'Profile eligible immediately; no documents or placements'],
            'pending-coc' => ['08 Pending COC', 'Completion records blocked by unfinished COC review'],
        ];
        foreach ($scenarios as $key => [$label, $expected]) {
            $name = 'RETENTION DEMO - '.$label;
            $email = 'retention-demo-'.$key.'@internconnect.invalid';
            $existing = DB::table('users')->where('email', $email)->first();
            if ($existing) {
                if ($existing->full_name !== $name || (string) $existing->role !== '0') {
                    throw new RuntimeException('A demo email is already used by an unexpected account. Nothing was overwritten.');
                }
                $profile = DB::table('students')->where('user_id', $existing->id)->first();
                if (! $profile || $profile->studentNum !== 'RETENTION-DEMO-'.strtoupper($key)) {
                    throw new RuntimeException('An existing demo profile was changed. It must be reviewed rather than overwritten.');
                }
                $students[$key] = ['user' => $existing->id, 'student' => $profile->id, 'name' => $name, 'fresh' => false];
            } else {
                $user = $this->insert('users', [
                    'first_name' => 'Retention Demo', 'last_name' => $label, 'full_name' => $name,
                    'email' => $email, 'password' => Hash::make(Str::random(64)), 'role' => '0', 'status' => '1',
                    'has_local_password' => false, 'created_at' => $old.' 08:00:00',
                    'last_activity_at' => $old.' 08:00:00', 'updated_at' => $old.' 08:00:00',
                ]);
                $student = $this->insert('students', [
                    'user_id' => $user, 'studentNum' => 'RETENTION-DEMO-'.strtoupper($key), 'email' => $email,
                    'full_name' => $name, 'course' => 'RETENTION DEMO', 'year_and_section' => '4-1',
                    'school_year_start' => (int) substr($old, 0, 4), 'school_year_end' => (int) substr($old, 0, 4) + 1,
                    'created_at' => $old.' 08:00:00', 'updated_at' => $old.' 08:00:00',
                ]);
                $students[$key] = ['user' => $user, 'student' => $student, 'name' => $name, 'fresh' => true];
                if (! in_array($key, ['incomplete', 'active-reader'], true)) {
                    $this->state('student', $user, $key === 'recent' ? $recent : $old, $key === 'held');
                }
                if (in_array($key, ['due', 'recent', 'held', 'incomplete'], true)) {
                    $this->requirement($students[$key], $this->pdf('requirements', $name.' - synthetic resume'));
                }
                if (in_array($key, ['due', 'pending-coc'], true)) {
                    $this->insert('completion_certificates', [
                        'student_id' => $student, 'student_user_id' => $user, 'file' => $this->pdf('completion-certificates', $name.' - synthetic COC'),
                        'original_name' => 'RETENTION-DEMO-COC.pdf', 'storage_directory' => 'completion-certificates',
                        'version' => 1, 'status' => $key === 'due' ? 'approved' : 'pending_coordinator',
                        'coordinator_reviewed_at' => $key === 'due' ? $old.' 09:00:00' : null,
                        'created_at' => $old.' 08:00:00', 'updated_at' => $old.' 09:00:00',
                    ]);
                }
                if ($key === 'due') {
                    $this->evaluation($students[$key], $old);
                }
            }
            $report[] = ['Student user', $students[$key]['user'], $name, $expected, $students[$key]['fresh'] ? 'Created' : 'Existing; unchanged'];
        }
        if ($students['shared-owner']['fresh'] && $students['active-reader']['fresh']) {
            $file = $this->pdf('requirements', 'RETENTION DEMO - shared document; both references must be considered');
            $this->requirement($students['shared-owner'], $file);
            $this->requirement($students['active-reader'], $file);
        } elseif ($students['shared-owner']['fresh'] !== $students['active-reader']['fresh']) {
            $fresh = $students['shared-owner']['fresh'] ? $students['shared-owner'] : $students['active-reader'];
            $existing = $students['shared-owner']['fresh'] ? $students['active-reader'] : $students['shared-owner'];
            $file = DB::table('file_requirements')->where('uploader_user_id', $existing['user'])->where('fileName', 'RETENTION DEMO Resume')->value('file');
            if (! $file || ! DB::table('upload_security_records')->where('filename', $file)->where('origin', 'retention_demo')->exists()
                || ! Storage::disk(config('upload_security.disk'))->exists('requirements/'.$file)) {
                throw new RuntimeException('The shared demo scenario has been changed. Existing test records were not reset; restore that scenario explicitly before seeding again.');
            }
            $this->requirement($fresh, $file);
        }
        foreach (['due' => '01 Expired Eligible MOA', 'blocked' => '02 Expired Active Placement MOA', 'current' => '03 Current MOA', 'held' => '04 Expired Held MOA'] as $key => $label) {
            $name = 'RETENTION DEMO - '.$label;
            $company = DB::table('companies')->where('company_name', $name)->first();
            $fresh = ! $company;
            if (! $company) {
                $expiry = $key === 'current' ? CarbonImmutable::today()->addYearNoOverflow()->toDateString() : $old;
                $id = $this->insert('companies', [
                    'company_name' => $name, 'company_address' => 'Synthetic test address only', 'company_rep' => 'RETENTION DEMO Supervisor',
                    'company_email' => 'retention-demo-company@internconnect.invalid', 'companyNo' => 'DEMO ONLY',
                    'uploader_name' => 'RETENTION DEMO Fixture', 'file' => $this->pdf('moa', $name),
                    'valid_until' => $expiry, 'date_notarized' => CarbonImmutable::parse($expiry)->subYearNoOverflow()->toDateString(),
                    'school_year' => substr($old, 0, 4).'-'.((int) substr($old, 0, 4) + 1), 'created_at' => $old.' 08:00:00', 'updated_at' => $old.' 08:00:00',
                ]);
                if ($key === 'blocked') {
                    $this->insert('company_student', ['company_id' => $id, 'student_id' => $students['active-reader']['student']], false);
                }
                if ($key === 'held') {
                    $this->state('moa', $id, null, true);
                }
                if ($key === 'due') {
                    $this->insert('moa_renewals', [
                        'company_id' => $id, 'previous_file' => $this->pdf('moa', $name.' - previous agreement'),
                        'new_file' => DB::table('companies')->where('id', $id)->value('file'),
                        'previous_valid_until' => CarbonImmutable::parse($old)->subYearNoOverflow()->toDateString(),
                        'new_date_notarized' => CarbonImmutable::parse($old)->subYearNoOverflow()->toDateString(),
                        'new_valid_until' => $old, 'renewed_at' => CarbonImmutable::parse($old)->subYearNoOverflow(),
                        'notes' => 'SYNTHETIC RETENTION TEST ONLY', 'created_at' => $old.' 08:00:00', 'updated_at' => $old.' 08:00:00',
                    ]);
                }
            } else {
                $id = $company->id;
                if ($company->uploader_name !== 'RETENTION DEMO Fixture') {
                    throw new RuntimeException('A demo company label belongs to an unexpected record. Nothing was overwritten.');
                }
            }
            $report[] = ['Company', $id, $name, match ($key) { 'due' => 'Eligible, including renewal documents', 'blocked' => 'Blocked by unfinished placement', 'held' => 'Blocked by temporary hold', default => 'Retention period not ended' }, $fresh ? 'Created' : 'Existing; unchanged'];
        }

        return $report;
    }

    private function insert(string $table, array $values, bool $withId = true): int
    {
        $values = array_intersect_key($values, array_flip(Schema::getColumnListing($table)));
        if (! $withId) {
            DB::table($table)->insert($values);

            return 0;
        }

        return (int) DB::table($table)->insertGetId($values);
    }

    private function state(string $type, int $id, ?string $date, bool $held): void
    {
        $this->insert('retention_states', [
            'subject_type' => $type, 'subject_id' => $id, 'completed_on' => $date, 'clearance_finalized_on' => $date,
            'hold_until' => $held ? CarbonImmutable::today()->addDays(30)->toDateString() : null,
            'hold_reason' => $held ? 'RETENTION DEMO: unresolved test review' : null, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function requirement(array $student, string $file): void
    {
        $this->insert('file_requirements', [
            'fileName' => 'RETENTION DEMO Resume', 'file' => $file, 'uploader_user_id' => $student['user'],
            'uploadedBy' => $student['name'], 'status' => '1', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function evaluation(array $student, string $date): void
    {
        $template = $this->insert('ojt_evaluation_templates', ['title' => 'RETENTION DEMO - inactive rubric', 'description' => 'Synthetic test only; never selected for real invitations', 'is_active' => false, 'version' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $item = $this->insert('ojt_evaluation_template_items', ['template_id' => $template, 'section' => 'RETENTION DEMO', 'label' => 'Synthetic performance item', 'input_type' => 'rating', 'display_order' => 1, 'is_required' => true, 'created_at' => now(), 'updated_at' => now()]);
        $request = $this->insert('ojt_evaluation_requests', [
            'student_id' => $student['user'], 'template_id' => $template, 'student_num' => 'RETENTION-DEMO-DUE',
            'student_name' => $student['name'], 'supervisor_name' => 'RETENTION DEMO Supervisor', 'supervisor_email' => 'retention-demo-supervisor@internconnect.invalid',
            'token' => Str::random(64), 'token_expires_at' => $date.' 08:00:00', 'submitted_at' => $date.' 08:00:00', 'status' => 'submitted', 'created_at' => $date.' 08:00:00', 'updated_at' => $date.' 08:00:00',
        ]);
        $this->insert('ojt_evaluations', [
            'request_id' => $request, 'template_id' => $template, 'supervisor_name' => 'RETENTION DEMO Supervisor',
            'responses_json' => json_encode([['item_id' => $item, 'section' => 'RETENTION DEMO', 'label' => 'Synthetic performance item', 'score' => 4]]),
            'comments' => 'SYNTHETIC RETENTION TEST ONLY', 'supervisor_confirmation' => true,
            'submitted_at' => $date.' 08:00:00', 'released_to_student_at' => $date.' 09:00:00', 'created_at' => $date.' 08:00:00', 'updated_at' => $date.' 09:00:00',
        ]);
    }

    private function pdf(string $directory, string $label): string
    {
        $filename = 'retention-demo-'.Str::uuid().'.pdf';
        $path = $directory.'/'.$filename;
        $disk = Storage::disk(config('upload_security.disk'));
        $pdf = new Dompdf(['isRemoteEnabled' => false]);
        $pdf->loadHtml('<h1>SYNTHETIC TEST DOCUMENT</h1><p>'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</p><p>RETENTION DEMO ONLY. No real student, academic, employment, or company information. Safe fixture for manual retention testing.</p>');
        $pdf->render();
        if ($disk->exists($path) || ! $disk->put($path, $pdf->output())) {
            throw new RuntimeException('Unable to create a new synthetic PDF. Existing files were not overwritten.');
        }
        $this->createdFiles[] = $path;
        $inspection = app(SecureUploadService::class)->inspectExisting($disk->path($path), $filename, 'pdf');
        app(UploadSecurityRecorder::class)->recordStored($path, $filename, $directory, 'pdf', $inspection, 'retention_demo');

        return $filename;
    }
}
