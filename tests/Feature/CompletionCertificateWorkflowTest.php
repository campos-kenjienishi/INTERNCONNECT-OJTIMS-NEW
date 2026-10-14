<?php

namespace Tests\Feature;

use App\Http\Controllers\CompletionCertificateController;
use App\Models\CompletionCertificate;
use App\Services\DocumentAccessService;
use App\Services\PrivateDocumentStorage;
use App\Services\RequirementUploadLock;
use App\Services\SecureDocumentResponse;
use App\Services\SecureUploadService;
use App\Services\UploadSecurityRecorder;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Mockery;
use Tests\TestCase;

class CompletionCertificateWorkflowTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = (string) config('database.default');
        config()->set('database.connections.coc_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        config()->set('database.default', 'coc_testing');
        DB::purge('coc_testing');
        $this->createTables();
        $this->seedWorkflow();
    }

    protected function tearDown(): void
    {
        DB::purge('coc_testing');
        config()->set('database.default', $this->originalConnection);
        parent::tearDown();
    }

    public function test_coordinator_can_approve_without_optional_professor_validation(): void
    {
        $certificate = CompletionCertificate::findOrFail(20);
        $controller = $this->controller();

        Session::put('loginId', 3);
        $controller->coordinatorApprove($certificate);

        $this->assertDatabaseHas('completion_certificates', [
            'id' => 20,
            'status' => CompletionCertificate::APPROVED,
            'professor_reviewed_by' => null,
            'coordinator_reviewed_by' => 3,
        ]);
        $this->assertTrue($certificate->fresh()->professorValidationWasSkipped());
    }

    public function test_professor_can_optionally_validate_before_coordinator_approval(): void
    {
        $certificate = CompletionCertificate::findOrFail(20);
        $controller = $this->controller();

        Session::put('loginId', 2);
        $controller->professorApprove($certificate);
        $this->assertDatabaseHas('completion_certificates', [
            'id' => 20,
            'status' => CompletionCertificate::PENDING_COORDINATOR,
            'professor_reviewed_by' => 2,
        ]);

        Session::put('loginId', 3);
        $controller->coordinatorApprove($certificate->fresh());
        $this->assertDatabaseHas('completion_certificates', [
            'id' => 20,
            'status' => CompletionCertificate::APPROVED,
            'coordinator_reviewed_by' => 3,
        ]);
        $this->assertTrue($certificate->fresh()->hasProfessorValidation());
        $this->assertFalse($certificate->fresh()->professorValidationWasSkipped());
    }

    public function test_coordinator_can_validate_a_submission_without_an_assigned_professor(): void
    {
        DB::table('completion_certificates')->where('id', 20)->update([
            'professor_id' => null,
            'status' => CompletionCertificate::PENDING_COORDINATOR,
        ]);

        Session::put('loginId', 3);
        $this->controller()->coordinatorApprove(CompletionCertificate::findOrFail(20));

        $this->assertDatabaseHas('completion_certificates', [
            'id' => 20,
            'professor_id' => null,
            'status' => CompletionCertificate::APPROVED,
            'coordinator_reviewed_by' => 3,
        ]);
    }

    public function test_coordinator_can_filter_certificates_by_program_and_school_year(): void
    {
        DB::table('users')->insert(['id' => 4, 'full_name' => 'Student Two', 'role' => 0]);
        DB::table('students')->insert([
            'id' => 11,
            'user_id' => 4,
            'studentNum' => '2026-00002',
            'adviser_name' => 'Professor One',
            'course' => 'Bachelor of Science in Hospitality Management',
            'school_year_start' => 2025,
            'school_year_end' => 2026,
        ]);
        DB::table('completion_certificates')->insert([
            'id' => 21,
            'student_id' => 11,
            'student_user_id' => 4,
            'professor_id' => 7,
            'file' => 'certificate-two.pdf',
            'storage_directory' => 'completion-certificates',
            'version' => 1,
            'status' => CompletionCertificate::PENDING_PROFESSOR,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Session::put('loginId', 3);
        $request = Request::create('/coordinator/completion-certificates', 'GET', [
            'program' => 'Bachelor of Science in Information Technology',
            'school_year' => '2026-2027',
            'per_page' => 25,
        ]);

        $view = $this->controller()->coordinatorIndex($request);
        $records = $view->getData()['records'];

        $this->assertSame(1, $records->total());
        $this->assertSame(25, $records->perPage());
        $this->assertSame(20, $records->first()->id);
        $this->assertEqualsCanonicalizing([
            'Bachelor of Science in Hospitality Management',
            'Bachelor of Science in Information Technology',
        ], $view->getData()['programs']->all());
        $this->assertEqualsCanonicalizing(['2025-2026', '2026-2027'], $view->getData()['schoolYears']->all());
    }

    public function test_student_certificate_history_is_paginated(): void
    {
        foreach (range(2, 7) as $version) {
            DB::table('completion_certificates')->insert([
                'student_id' => 10,
                'student_user_id' => 1,
                'professor_id' => 7,
                'file' => "certificate-{$version}.pdf",
                'storage_directory' => 'completion-certificates',
                'version' => $version,
                'status' => CompletionCertificate::COORDINATOR_REJECTED,
                'created_at' => now()->subDays($version),
                'updated_at' => now()->subDays($version),
            ]);
        }

        Session::put('loginId', 1);
        $history = $this->controller()->studentIndex()->getData()['history'];

        $this->assertSame(7, $history->total());
        $this->assertSame(5, $history->perPage());
        $this->assertCount(5, $history->items());
        $this->assertSame(7, $history->first()->version);
    }

    private function controller(): CompletionCertificateController
    {
        return new CompletionCertificateController(
            Mockery::mock(SecureUploadService::class),
            Mockery::mock(PrivateDocumentStorage::class),
            Mockery::mock(RequirementUploadLock::class),
            Mockery::mock(DocumentAccessService::class),
            Mockery::mock(SecureDocumentResponse::class),
            Mockery::mock(UploadSecurityRecorder::class)
        );
    }

    private function seedWorkflow(): void
    {
        DB::table('users')->insert([
            ['id' => 1, 'full_name' => 'Student One', 'role' => 0],
            ['id' => 2, 'full_name' => 'Professor One', 'role' => 2],
            ['id' => 3, 'full_name' => 'Coordinator One', 'role' => 1],
        ]);
        DB::table('professors')->insert(['id' => 7, 'user_id' => 2, 'full_name' => 'Professor One']);
        DB::table('students')->insert([
            'id' => 10,
            'user_id' => 1,
            'studentNum' => '2026-00001',
            'adviser_name' => 'Professor One',
            'course' => 'Bachelor of Science in Information Technology',
            'school_year_start' => 2026,
            'school_year_end' => 2027,
        ]);
        DB::table('completion_certificates')->insert([
            'id' => 20,
            'student_id' => 10,
            'student_user_id' => 1,
            'professor_id' => 7,
            'file' => 'certificate.pdf',
            'storage_directory' => 'completion-certificates',
            'version' => 1,
            'status' => CompletionCertificate::PENDING_PROFESSOR,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->unsignedTinyInteger('role');
        });
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('full_name');
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('studentNum')->nullable();
            $table->string('adviser_name')->nullable();
            $table->string('course')->nullable();
            $table->string('year_and_section')->nullable();
            $table->unsignedSmallInteger('school_year_start')->nullable();
            $table->unsignedSmallInteger('school_year_end')->nullable();
        });
        Schema::create('completion_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('student_user_id');
            $table->unsignedBigInteger('professor_id')->nullable();
            $table->string('file');
            $table->string('original_name')->nullable();
            $table->string('storage_directory');
            $table->unsignedInteger('version')->default(1);
            $table->string('status');
            $table->text('professor_remarks')->nullable();
            $table->unsignedBigInteger('professor_reviewed_by')->nullable();
            $table->timestamp('professor_reviewed_at')->nullable();
            $table->text('coordinator_remarks')->nullable();
            $table->unsignedBigInteger('coordinator_reviewed_by')->nullable();
            $table->timestamp('coordinator_reviewed_at')->nullable();
            $table->timestamp('superseded_at')->nullable();
            $table->unsignedBigInteger('legacy_file_requirement_id')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->unsignedBigInteger('affected_user_id')->nullable();
            $table->string('affected_name')->nullable();
            $table->string('action');
            $table->string('module');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }
}
