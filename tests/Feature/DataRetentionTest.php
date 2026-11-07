<?php

namespace Tests\Feature;

use App\Http\Controllers\DataRetentionController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Services\DataRetentionService;
use App\Services\RetentionFileCleaner;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class DataRetentionTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config(['database.connections.retention_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true], 'database.default' => 'retention_testing']);
        DB::purge('retention_testing');
        Carbon::setTestNow('2026-11-07 12:00:00');
        CarbonImmutable::setTestNow('2026-11-07 12:00:00');
        config(['retention.disposal_enabled' => false, 'retention.requirements_years' => 1, 'retention.completion_records_years' => 2, 'retention.profiles_years' => 2, 'retention.moa_years' => 2, 'upload_security.disk' => 'secure_uploads']);
        Storage::fake('secure_uploads');
        Storage::fake('public');
        Storage::fake('retention_legacy');
        config(['upload_security.legacy_directory' => Storage::disk('retention_legacy')->path('')]);
        Http::preventStrayRequests();
        $maintenance = Mockery::mock(MaintenanceMode::class);
        $maintenance->shouldReceive('active')->andReturn(false);
        $this->app->instance(MaintenanceMode::class, $maintenance);
        $this->createTables();
        DB::table('users')->insert([
            ['id' => 1, 'full_name' => 'Coordinator', 'role' => '1', 'email' => 'coordinator@example.test', 'created_at' => '2020-01-01'],
            ['id' => 2, 'full_name' => 'Student One', 'role' => '0', 'email' => 'one@example.test', 'created_at' => '2020-01-01'],
            ['id' => 3, 'full_name' => 'Student Two', 'role' => '0', 'email' => 'two@example.test', 'created_at' => '2020-01-01'],
        ]);
        DB::table('students')->insert([
            ['id' => 10, 'user_id' => 2, 'studentNum' => 'TEST-ONE', 'course' => 'BSIT'],
            ['id' => 11, 'user_id' => 3, 'studentNum' => 'TEST-TWO', 'course' => 'BSIT'],
        ]);
        $this->withSession(['loginId' => 1]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        DB::purge('retention_testing');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public function test_old_account_cleanup_cannot_delete_any_student(): void
    {
        $this->artisan('app:delete-old-users')->assertSuccessful();
        $this->assertDatabaseCount('users', 3);
    }

    public function test_preview_requires_both_dates_and_does_not_infer_them_from_age(): void
    {
        $this->requirement();
        $this->assertFalse($this->plan()['eligible']);
        $this->state(['completed_on' => '2020-01-01', 'clearance_finalized_on' => null]);
        $this->assertFalse($this->plan()['eligible']);
        $this->assertDatabaseCount('file_requirements', 1);
    }

    public function test_later_clearance_date_controls_the_retention_boundary(): void
    {
        $this->requirement();
        $this->state(['completed_on' => '2023-01-01', 'clearance_finalized_on' => '2025-11-08']);
        $this->assertFalse($this->plan()['eligible']);
        $this->assertSame('2026-11-08', $this->plan()['due_on']);
        $this->state(['clearance_finalized_on' => '2025-11-07']);
        $this->assertTrue($this->plan()['eligible']);
    }

    public function test_hold_blocks_disposal_until_after_the_hold_date(): void
    {
        $this->requirement();
        $this->state(['hold_until' => '2026-11-07', 'hold_reason' => 'Pending review']);
        $this->assertFalse($this->plan()['eligible']);
        $this->state(['hold_until' => '2026-11-06']);
        $this->assertTrue($this->plan()['eligible']);
    }

    public function test_preview_only_mode_blocks_direct_execution(): void
    {
        $this->requirement();
        $this->state();
        try {
            $plan = $this->plan();
            app(DataRetentionService::class)->dispose('requirements', 2, $plan['fingerprint'], 1);
            $this->fail('Preview-only disposal must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('file_requirements', 1);
            $this->assertDatabaseCount('retention_disposals', 0);
            Storage::disk('secure_uploads')->assertExists('requirements/requirement.pdf');
        }
    }

    public function test_requirement_disposal_removes_only_regular_documents_and_physical_copies(): void
    {
        $this->requirement();
        $this->requirement('moa.pdf', 'Notarized MOA');
        $this->requirement('coc.pdf', 'Certificate of Completion');
        $this->state();
        $id = $this->execute('requirements');
        $this->assertDatabaseCount('file_requirements', 2);
        $this->assertDatabaseHas('file_requirements', ['file' => 'moa.pdf']);
        Storage::disk('secure_uploads')->assertMissing('requirements/requirement.pdf');
        Storage::disk('retention_legacy')->assertMissing('requirement.pdf');
        $this->assertDatabaseHas('retention_disposals', ['id' => $id, 'status' => 'completed']);
        $this->assertSame(0, DB::table('retention_file_deletions')->whereNotNull('path')->count());
    }

    public function test_shared_file_is_preserved_even_when_an_eligible_reference_is_removed(): void
    {
        $this->requirement();
        DB::table('file_requirements')->insert(['fileName' => 'Resume', 'file' => 'requirement.pdf', 'uploader_user_id' => 3, 'uploadedBy' => 'Student Two']);
        $this->state();
        $this->execute('requirements');
        Storage::disk('secure_uploads')->assertExists('requirements/requirement.pdf');
        Storage::disk('retention_legacy')->assertExists('requirement.pdf');
        $this->assertDatabaseHas('retention_file_deletions', ['status' => 'retained_shared']);
        $this->assertDatabaseHas('file_requirements', ['uploader_user_id' => 3]);
    }

    public function test_changed_preview_cannot_be_executed(): void
    {
        $this->requirement();
        $this->state();
        $plan = $this->plan();
        $this->requirement('new.pdf');
        config(['retention.disposal_enabled' => true]);
        try {
            app(DataRetentionService::class)->dispose('requirements', 2, $plan['fingerprint'], 1);
            $this->fail('Stale disposal must fail.');
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('file_requirements', 2);
            $this->assertDatabaseCount('retention_disposals', 0);
        }
    }

    public function test_failed_physical_cleanup_is_durable_and_retryable(): void
    {
        $this->requirement();
        $this->state();
        $manager = Storage::getFacadeRoot();
        $root = Storage::disk('secure_uploads')->path('');
        $disk = Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('path')->with('')->andReturn($root);
        $disk->shouldReceive('delete')->andReturn(false);
        Storage::shouldReceive('disk')->with('secure_uploads')->andReturn($disk);
        $id = $this->execute('requirements');
        $this->assertDatabaseHas('retention_disposals', ['id' => $id, 'status' => 'file_cleanup_pending']);
        $this->assertDatabaseHas('retention_file_deletions', ['status' => 'failed', 'path' => 'requirements/requirement.pdf']);
        Storage::swap($manager);
        app(RetentionFileCleaner::class)->process($id);
        Storage::disk('secure_uploads')->assertMissing('requirements/requirement.pdf');
        $this->assertDatabaseHas('retention_disposals', ['id' => $id, 'status' => 'completed']);
    }

    public function test_ambiguous_legacy_ownership_blocks_disposal(): void
    {
        $this->requirement();
        $this->state();
        DB::table('file_requirements')->update(['uploader_user_id' => null]);
        DB::table('users')->where('id', 3)->update(['full_name' => 'Student One']);
        $this->assertFalse($this->plan()['eligible']);
        $this->assertStringContainsString('Ambiguous', $this->plan()['reason']);
    }

    public function test_unsafe_file_path_rolls_back_database_disposal(): void
    {
        $this->requirement('../outside.pdf');
        $this->state();
        config(['retention.disposal_enabled' => true]);
        try {
            $plan = $this->plan();
            app(DataRetentionService::class)->dispose('requirements', 2, $plan['fingerprint'], 1);
            $this->fail('Unsafe path must fail.');
        } catch (\RuntimeException $exception) {
            $this->assertDatabaseCount('file_requirements', 1);
            $this->assertDatabaseCount('retention_disposals', 0);
        }
    }

    public function test_expired_moa_is_blocked_by_active_or_held_student_placements(): void
    {
        $this->company();
        DB::table('company_student')->insert(['company_id' => 20, 'student_id' => 10]);
        $this->assertFalse($this->plan('moa', 20)['eligible']);
        $this->state(['hold_until' => '2027-01-01']);
        $this->assertFalse($this->plan('moa', 20)['eligible']);
        $this->state(['hold_until' => null]);
        $this->assertTrue($this->plan('moa', 20)['eligible']);
    }

    public function test_moa_disposal_deletes_history_and_documents_but_not_students(): void
    {
        $this->company();
        DB::table('moa_renewals')->insert(['company_id' => 20, 'previous_file' => 'old.pdf', 'new_file' => 'moa.pdf']);
        Storage::disk('secure_uploads')->put('moa/old.pdf', 'old agreement');
        $this->execute('moa', 20);
        $this->assertDatabaseCount('companies', 0);
        $this->assertDatabaseCount('moa_renewals', 0);
        $this->assertDatabaseCount('users', 3);
        Storage::disk('secure_uploads')->assertMissing('moa/moa.pdf');
        Storage::disk('secure_uploads')->assertMissing('moa/old.pdf');
    }

    public function test_completion_records_use_two_years_and_remove_evaluations_and_coc_history(): void
    {
        $this->state();
        DB::table('completion_certificates')->insert(['student_user_id' => 2, 'student_id' => 10, 'file' => 'coc.pdf', 'storage_directory' => 'completion-certificates']);
        DB::table('ojt_evaluation_requests')->insert(['id' => 30, 'student_id' => 2, 'status' => 'submitted']);
        DB::table('ojt_evaluations')->insert(['request_id' => 30]);
        Storage::disk('secure_uploads')->put('completion-certificates/coc.pdf', 'COC');
        $this->assertSame('2026-11-07', $this->plan('completion_records')['due_on']);
        $this->execute('completion_records');
        $this->assertDatabaseCount('completion_certificates', 0);
        $this->assertDatabaseCount('ojt_evaluation_requests', 0);
        $this->assertDatabaseCount('ojt_evaluations', 0);
        $this->assertDatabaseCount('users', 3);
        Storage::disk('secure_uploads')->assertMissing('completion-certificates/coc.pdf');
    }

    public function test_profile_disposal_requires_other_retained_records_to_be_resolved(): void
    {
        $this->state();
        $this->requirement();
        $this->assertFalse($this->plan('student_profile')['eligible']);
        $this->execute('requirements');
        DB::table('o_j_t_information')->insert(['studentNum' => 'TEST-ONE']);
        DB::table('users')->where('id', 2)->update(['profile_photo' => 'profile_photos/student.png']);
        Storage::disk('public')->put('profile_photos/student.png', 'photo');
        $this->execute('student_profile');
        $this->assertDatabaseMissing('users', ['id' => 2]);
        $this->assertDatabaseMissing('students', ['id' => 10]);
        $this->assertDatabaseCount('o_j_t_information', 0);
        Storage::disk('public')->assertMissing('profile_photos/student.png');
        $this->assertDatabaseHas('users', ['id' => 3]);
    }

    public function test_pending_coc_prevents_completion_record_disposal(): void
    {
        $this->state();
        DB::table('completion_certificates')->insert(['student_user_id' => 2, 'student_id' => 10, 'file' => 'coc.pdf', 'storage_directory' => 'completion-certificates', 'status' => 'pending_coordinator']);
        $this->assertFalse($this->plan('completion_records')['eligible']);
    }

    public function test_profile_with_a_live_placement_cannot_be_disposed(): void
    {
        $this->state();
        $this->company();
        DB::table('company_student')->insert(['company_id' => 20, 'student_id' => 10]);
        $this->assertFalse($this->plan('student_profile')['eligible']);
    }

    public function test_coordinator_cannot_dispose_another_coordinators_preview(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->state();
        $this->requirement();
        $token = $this->postJson(route('retention.preview'), ['category' => 'requirements', 'subject_id' => 2])->assertOk()->json('token');
        DB::table('users')->where('id', 3)->update(['role' => 1]);
        $this->withSession(['loginId' => 3])->postJson(route('retention.dispose'), ['token' => $token, 'confirmation' => 'DISPOSE'])->assertUnprocessable();
        $this->assertDatabaseCount('file_requirements', 1);
    }

    public function test_successful_http_disposal_requires_confirmation_and_cannot_be_replayed(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->state();
        $this->requirement();
        config(['retention.disposal_enabled' => true]);
        $token = $this->postJson(route('retention.preview'), ['category' => 'requirements', 'subject_id' => 2])->assertOk()->json('token');
        $this->postJson(route('retention.dispose'), ['token' => $token, 'confirmation' => 'DELETE'])->assertUnprocessable();
        $this->postJson(route('retention.dispose'), ['token' => $token, 'confirmation' => 'DISPOSE'])->assertOk()->assertJsonPath('status', 'completed');
        $this->postJson(route('retention.dispose'), ['token' => $token, 'confirmation' => 'DISPOSE'])->assertUnprocessable();
        $this->assertDatabaseCount('retention_disposals', 1);
    }

    public function test_preview_command_never_deletes_records_or_files(): void
    {
        $this->state();
        $this->requirement();
        $this->artisan('retention:preview requirements 2')->assertSuccessful();
        $this->assertDatabaseCount('file_requirements', 1);
        $this->assertDatabaseCount('retention_disposals', 0);
        Storage::disk('secure_uploads')->assertExists('requirements/requirement.pdf');
    }

    public function test_non_coordinators_cannot_access_retention_endpoints(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->withSession(['loginId' => 2])->postJson(route('retention.preview'), ['category' => 'requirements', 'subject_id' => 3])->assertForbidden();
        $this->postJson(route('retention.update'), ['subject_type' => 'student', 'subject_id' => 3])->assertForbidden();
        $this->postJson(route('retention.dispose'), ['token' => 'invalid', 'confirmation' => 'DISPOSE'])->assertForbidden();
        $this->postJson(route('retention.retry', 1))->assertForbidden();
    }

    public function test_preview_tokens_are_actor_bound_expiring_and_not_forgeable(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->state();
        $this->requirement();
        $token = $this->postJson(route('retention.preview'), ['category' => 'requirements', 'subject_id' => 2])->assertOk()->json('token');
        config(['retention.disposal_enabled' => true]);
        $this->postJson(route('retention.dispose'), ['token' => $token.'invalid', 'confirmation' => 'DISPOSE'])->assertUnprocessable();
        Carbon::setTestNow('2026-11-07 12:06:00');
        $this->postJson(route('retention.dispose'), ['token' => $token, 'confirmation' => 'DISPOSE'])->assertUnprocessable();
        $this->assertDatabaseCount('file_requirements', 1);
    }

    public function test_dates_must_be_explicit_valid_and_confirmed(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $payload = ['subject_type' => 'student', 'subject_id' => 2, 'completed_on' => '2026-12-01', 'clearance_finalized_on' => '2026-01-01', 'completion_confirmed' => 1];
        $this->postJson(route('retention.update'), $payload)->assertUnprocessable();
        $payload['completed_on'] = '2024-01-01';
        $payload['clearance_finalized_on'] = '2024-02-01';
        $payload['completion_confirmed'] = 0;
        $this->postJson(route('retention.update'), $payload)->assertUnprocessable();
        $payload['completion_confirmed'] = 1;
        $this->postJson(route('retention.update'), $payload)->assertRedirect();
        $this->assertDatabaseHas('retention_states', ['subject_id' => 2, 'completed_on' => '2024-01-01', 'clearance_finalized_on' => '2024-02-01']);
        $this->assertDatabaseCount('retention_disposals', 0);
    }

    public function test_workspace_renders_policy_preview_and_pagination(): void
    {
        View::share('errors', new ViewErrorBag());
        $workspace = app(DataRetentionController::class)->workspace(request(), app(DataRetentionService::class));
        $this->view('partials.data-retention', ['retentionWorkspace' => $workspace])
            ->assertSee('Preview-only mode')->assertSee('Data Retention')->assertSee('Clearance finalized')->assertSee('No disposals executed.')
            ->assertSee('retention-subject-table')->assertSee('retentionDetailsModal')->assertSee('retention-category')
            ->assertDontSee('<article class="retention-record">', false);
        $html = view('partials.data-retention', ['retentionWorkspace' => $workspace])->render();
        preg_match('/<details[^>]*id="data-retention"[^>]*>/', $html, $matches);
        $this->assertStringNotContainsString(' open', $matches[0]);
        $this->assertSame(1, substr_count($html, 'id="retention-details-form"'));
        request()->query->set('retention_search', 'Student');
        $html = view('partials.data-retention', ['retentionWorkspace' => $workspace])->render();
        preg_match('/<details[^>]*id="data-retention"[^>]*>/', $html, $matches);
        $this->assertStringContainsString(' open', $matches[0]);
    }

    public function test_demo_seeding_is_confirmed_repeatable_and_does_not_change_existing_records(): void
    {
        Schema::create('ojt_evaluation_templates', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->boolean('is_active');
        });
        Schema::create('ojt_evaluation_template_items', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('template_id'); $table->string('label');
        });
        (require database_path('migrations/2026_10_11_000001_create_upload_security_records_table.php'))->up();
        $this->artisan('retention:seed-demo')->assertFailed();
        $this->assertDatabaseCount('users', 3);
        $this->artisan('retention:seed-demo --confirm')->assertSuccessful();
        $this->assertDatabaseCount('users', 11);
        $this->assertDatabaseCount('companies', 4);
        $this->assertDatabaseHas('users', ['id' => 2, 'full_name' => 'Student One']);
        $this->assertFalse(config('retention.disposal_enabled'));
        $due = DB::table('users')->where('email', 'retention-demo-due@internconnect.invalid')->value('id');
        $held = DB::table('users')->where('email', 'retention-demo-held@internconnect.invalid')->value('id');
        $this->assertTrue($this->plan('requirements', $due)['eligible']);
        $this->assertTrue($this->plan('completion_records', $due)['eligible']);
        $this->assertFalse($this->plan('requirements', $held)['eligible']);
        $files = DB::table('upload_security_records')->count();
        $this->assertGreaterThan(0, $files);
        $this->artisan('retention:seed-demo --confirm')->assertSuccessful();
        $this->assertDatabaseCount('users', 11);
        $this->assertDatabaseCount('companies', 4);
        $this->assertDatabaseCount('upload_security_records', $files);
        $this->assertDatabaseCount('retention_disposals', 0);
        $path = DB::table('upload_security_records')->value('storage_path');
        DB::table('companies')->where('company_name', 'RETENTION DEMO - 01 Expired Eligible MOA')->update(['uploader_name' => 'Manually changed fixture']);
        $this->artisan('retention:seed-demo --confirm')->assertFailed();
        Storage::disk('secure_uploads')->assertExists($path);
        $this->assertDatabaseCount('users', 11);
        $this->assertDatabaseCount('upload_security_records', $files);
    }

    public function test_demo_seeding_is_forbidden_in_production(): void
    {
        $this->app['env'] = 'production';
        $this->artisan('retention:seed-demo --confirm')->assertFailed();
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('companies', 0);
    }

    public function test_demo_seeding_rejects_a_hosted_app_url_even_when_environment_is_local(): void
    {
        $this->app['env'] = 'local';
        config(['app.url' => 'https://hosted.example.invalid']);
        $this->artisan('retention:seed-demo --confirm')->assertFailed();
        $this->assertDatabaseCount('users', 3);
    }

    private function state(array $values = []): void
    {
        DB::table('retention_states')->updateOrInsert(['subject_type' => 'student', 'subject_id' => 2], array_merge([
            'completed_on' => '2024-01-01', 'clearance_finalized_on' => '2024-11-07', 'hold_until' => null,
        ], $values));
    }

    private function requirement(string $file = 'requirement.pdf', string $name = 'Resume'): void
    {
        DB::table('file_requirements')->insert(['fileName' => $name, 'file' => $file, 'uploader_user_id' => 2, 'uploadedBy' => 'Student One']);
        if (! str_contains($file, '..')) {
            Storage::disk('secure_uploads')->put('requirements/'.$file, 'test file');
            Storage::disk('retention_legacy')->put($file, 'legacy copy');
        }
    }

    private function company(): void
    {
        DB::table('companies')->insert(['id' => 20, 'company_name' => 'Test Company', 'file' => 'moa.pdf', 'valid_until' => '2024-11-07', 'uploader_name' => 'Coordinator']);
        Storage::disk('secure_uploads')->put('moa/moa.pdf', 'agreement');
    }

    private function plan(string $category = 'requirements', int $id = 2): array
    {
        return app(DataRetentionService::class)->preview($category, $id);
    }

    private function execute(string $category, int $id = 2): int
    {
        config(['retention.disposal_enabled' => true]);
        $plan = $this->plan($category, $id);
        $this->assertTrue($plan['eligible'], $plan['reason']);

        return app(DataRetentionService::class)->dispose($category, $id, $plan['fingerprint'], 1);
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('full_name'); $table->string('role'); $table->string('email'); $table->string('profile_photo')->nullable(); $table->timestamp('last_activity_at')->nullable(); $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('studentNum'); $table->string('course'); $table->string('profile_photo')->nullable();
        });
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); $table->string('company_name'); $table->string('file')->nullable(); $table->string('uploader_name')->nullable(); $table->string('valid_until')->nullable(); $table->text('student_names_display')->nullable();
        });
        Schema::create('file_requirements', function (Blueprint $table) {
            $table->id(); $table->string('fileName'); $table->string('file'); $table->string('uploadedBy')->nullable(); $table->unsignedBigInteger('uploader_user_id')->nullable();
        });
        Schema::create('completion_certificates', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('student_user_id'); $table->unsignedBigInteger('student_id'); $table->string('file'); $table->string('storage_directory'); $table->string('status')->default('approved'); $table->timestamp('superseded_at')->nullable();
        });
        Schema::create('ojt_evaluation_requests', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('student_id'); $table->string('status');
        });
        Schema::create('ojt_evaluations', function (Blueprint $table) {
            $table->id(); $table->foreignId('request_id')->constrained('ojt_evaluation_requests')->cascadeOnDelete();
        });
        Schema::create('moa_renewals', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete(); $table->string('previous_file')->nullable(); $table->string('new_file')->nullable();
        });
        Schema::create('company_student', function (Blueprint $table) { $table->unsignedBigInteger('company_id'); $table->unsignedBigInteger('student_id'); });
        Schema::create('o_j_t_information', function (Blueprint $table) { $table->id(); $table->string('studentNum'); });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable(); $table->string('user_name')->nullable(); $table->string('user_role')->nullable(); $table->unsignedBigInteger('affected_user_id')->nullable(); $table->string('affected_name')->nullable(); $table->string('module'); $table->string('action'); $table->text('description')->nullable(); $table->string('ip_address')->nullable(); $table->timestamps();
        });
        (require database_path('migrations/2026_11_07_000001_create_data_retention_tables.php'))->up();
    }
}
