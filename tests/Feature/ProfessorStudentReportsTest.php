<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportsController;
use App\Services\ReportAiInsightService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ProfessorStudentReportsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'professor_reports_testing',
            'database.connections.professor_reports_testing' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            ],
            'cache.default' => 'array',
        ]);
        DB::purge('professor_reports_testing');
        Http::preventStrayRequests();
        Mail::fake();
        Session::flush();
        $this->mock(ReportAiInsightService::class)->shouldReceive('summarize')->andReturn([
            'summary' => 'Scoped report summary', 'key_findings' => [], 'watchouts' => [], 'recommendations' => [],
        ]);
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('role');
            $table->integer('status')->default(1);
        });
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            foreach (['room', 'adviser_name', 'course', 'semester', 'school_year_start', 'school_year_end'] as $column) {
                $table->string($column)->nullable();
            }
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('class_id')->nullable();
            foreach (['studentNum', 'course', 'adviser_name', 'year_and_section'] as $column) {
                $table->string($column)->nullable();
            }
        });
        Schema::create('o_j_t_information', function (Blueprint $table) {
            $table->id();
            $table->string('studentNum');
            $table->string('company_name');
        });
        DB::table('users')->insert([
            ['id' => 1, 'full_name' => 'Professor Alpha', 'role' => 2],
            ['id' => 2, 'full_name' => 'Professor Beta', 'role' => 2],
            ['id' => 3, 'full_name' => 'Professor Empty', 'role' => 2],
            ['id' => 4, 'full_name' => 'Coordinator', 'role' => 1],
        ]);
        $this->addClass(10, 'Professor Alpha', 'BSIT', '1st Sem', '2026', '2027');
        $this->addClass(11, 'Professor Alpha', 'BSECE', '2nd Semester', '2025', '2026');
        $this->addClass(20, 'Professor Beta', 'Private Program', '1st Semester', '2024', '2025');
        $this->addStudent(100, 10, 'Student Alpha', 'BSIT', 'Professor Alpha');
        $this->addStudent(101, 11, 'Student Second Class', 'BSECE', 'Professor Alpha');
        $this->addStudent(102, 20, 'Student Outside Scope', 'Private Program', 'Professor Alpha');
        $this->addStudent(103, null, 'Unassigned Student', 'BSIT', 'Professor Alpha');
        DB::table('users')->where('id', 101)->update(['status' => 0]);
        Session::put('loginId', 1);
    }

    protected function tearDown(): void
    {
        DB::purge('professor_reports_testing');
        parent::tearDown();
    }

    private function addClass(int $id, string $professor, string $program, string $semester, ?string $start, ?string $end): void
    {
        DB::table('classes')->insert([
            'id' => $id, 'room' => 'Class ' . $id, 'adviser_name' => $professor, 'course' => $program,
            'semester' => $semester, 'school_year_start' => $start, 'school_year_end' => $end,
        ]);
    }

    private function addStudent(int $id, ?int $classId, string $name, string $program, string $professor): void
    {
        DB::table('users')->insert(['id' => $id, 'full_name' => $name, 'role' => 0]);
        DB::table('students')->insert([
            'user_id' => $id, 'class_id' => $classId, 'studentNum' => 'QA-' . $id,
            'course' => $program, 'adviser_name' => $professor, 'year_and_section' => '3-1',
        ]);
        DB::table('o_j_t_information')->insert(['studentNum' => 'QA-' . $id, 'company_name' => 'Company ' . $id]);
    }

    private function report(array $filters = [])
    {
        return app(ReportsController::class)->professorStudentReports(Request::create('/professor/reports', 'POST', $filters));
    }

    public function test_report_only_contains_active_students_in_owned_classes(): void
    {
        $data = $this->report()->getData();
        $this->assertSame([100], array_map(fn ($row) => $row['student']->id, $data['studentData']));
        $this->assertSame('Company 100', $data['studentData'][0]['ojt']->company_name);
        $this->assertSame(['2026-2027', '2025-2026'], $data['schoolYears']->all());
        $this->assertEqualsCanonicalizing(['BSIT', 'BSECE'], $data['course']->pluck('course')->all());
        $html = $this->report()->render();
        $this->assertStringContainsString('Student Alpha', $html);
        $this->assertStringNotContainsString('Student Outside Scope', $html);
        $this->assertStringNotContainsString('Private Program', $html);
        $this->assertStringNotContainsString('Company 102', $html);
    }

    public function test_class_program_school_year_and_normalized_semester_filters(): void
    {
        DB::table('users')->where('id', 101)->update(['status' => 1]);
        $data = $this->report(['class_id' => 10, 'course' => 'BSIT', 'school_year' => '2026 - 2027', 'semester' => '1st Semester'])->getData();
        $this->assertSame([100], array_map(fn ($row) => $row['student']->id, $data['studentData']));
        $this->assertSame('Class 10', $data['classLabel']);
        $this->assertSame('2026-2027', $data['selectedSchoolYear']);
        $this->assertCount(0, $this->report(['class_id' => 10, 'semester' => '2nd Semester'])->getData()['studentData']);
        $this->assertCount(1, $this->report(['course' => 'BSECE', 'school_year' => '2025-2026'])->getData()['studentData']);
        $this->addClass(12, 'Professor Alpha', 'BSIT', 'Summer', '2026', '2027');
        $this->addStudent(104, 12, 'Summer Student', 'BSIT', 'Professor Alpha');
        $this->assertCount(1, $this->report(['semester' => 'Summer Term'])->getData()['studentData']);
    }

    public function test_all_programs_never_broadens_class_ownership(): void
    {
        $this->assertCount(0, $this->report(['course' => 'Private Program'])->getData()['studentData']);
        $this->assertCount(1, $this->report(['course' => '', 'school_year' => '', 'semester' => ''])->getData()['studentData']);
        Session::put('loginId', 2);
        $data = $this->report()->getData();
        $this->assertSame([102], array_map(fn ($row) => $row['student']->id, $data['studentData']));
    }

    public function test_tampered_class_id_is_rejected(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(0);
        try {
            $this->report(['class_id' => 20]);
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            throw $exception;
        }
    }

    public function test_professor_without_classes_gets_an_empty_report(): void
    {
        Session::put('loginId', 3);
        $data = $this->report()->getData();
        $this->assertCount(0, $data['studentData']);
        $this->assertCount(0, $data['classes']);
        $this->assertCount(0, $data['course']);
    }

    public function test_legacy_membership_only_applies_to_an_owned_undated_class(): void
    {
        $this->addClass(13, 'Professor Alpha', 'BSIT', '1st Semester', null, null);
        $data = $this->report(['class_id' => 13])->getData();
        $this->assertSame([103], array_map(fn ($row) => $row['student']->id, $data['studentData']));
        $this->assertCount(1, $this->report(['class_id' => 10])->getData()['studentData']);
    }

    public function test_professor_layout_and_print_context_do_not_use_coordinator_identity(): void
    {
        $html = $this->report(['class_id' => 10])->render();
        $this->assertStringContainsString('Professor Portal', $html);
        $this->assertStringContainsString('Student OJT Report', $html);
        $this->assertStringContainsString('preparedByRole: "OJT Professor"', $html);
        $this->assertStringContainsString('coordinatorName: "Professor Alpha"', $html);
        $this->assertStringContainsString('classLabel: "Class 10"', $html);
        $this->assertStringContainsString('useAppliedFilters: true', $html);
        $this->assertStringNotContainsString('id="sendEmailForm"', $html);
        $this->assertStringNotContainsString('href="' . url('/studentLists') . '"', $html);
    }

    public function test_shared_coordinator_layout_preserves_its_defaults(): void
    {
        $data = $this->report()->getData();
        unset($data['isProfessorReport']);
        $html = view('ojtCoordinator.reportsT', $data)->render();
        $this->assertStringContainsString('preparedByRole: "OJT Coordinator"', $html);
        $this->assertStringContainsString('useAppliedFilters: false', $html);
        $this->assertStringContainsString('action="' . route('studentojt.report.generate') . '"', $html);
    }

    public function test_student_cannot_call_professor_report(): void
    {
        Session::put('loginId', 100);
        $this->expectException(HttpException::class);
        try {
            $this->report();
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            throw $exception;
        }
    }

    public function test_route_requires_session_authentication_and_professor_role(): void
    {
        $route = app('router')->getRoutes()->getByName('professor.reports');
        $this->assertContains('auth.session.custom', $route->gatherMiddleware());
        $this->assertContains('role:2', $route->gatherMiddleware());
        Session::flush();
        $this->expectException(HttpException::class);
        try {
            $this->report();
        } catch (HttpException $exception) {
            $this->assertSame(401, $exception->getStatusCode());
            throw $exception;
        }
    }
}
