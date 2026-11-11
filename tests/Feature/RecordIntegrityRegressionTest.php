<?php

namespace Tests\Feature;

use App\Http\Controllers\PassDocuController;
use App\Http\Controllers\StudentController;
use App\Models\FileRequirement;
use App\Models\OJTInformation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RecordIntegrityRegressionTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config(['database.connections.integrity_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ], 'database.default' => 'integrity_testing']);
        DB::purge('integrity_testing');
        Mail::fake();
        config(['cache.default' => 'array']);
        Http::preventStrayRequests();
        Session::flush();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('studentNum')->nullable();
            $table->integer('role');
        });
        DB::table('users')->insert([
            ['id' => 1, 'full_name' => 'Coordinator', 'studentNum' => null, 'role' => 1],
            ['id' => 2, 'full_name' => 'Assigned Professor', 'studentNum' => null, 'role' => 2],
            ['id' => 3, 'full_name' => 'Other Professor', 'studentNum' => null, 'role' => 2],
            ['id' => 10, 'full_name' => 'Student Alpha', 'studentNum' => 'QA-10', 'role' => 0],
        ]);
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('studentNum')->nullable();
        });
        Schema::create('o_j_t_information', function (Blueprint $table) {
            $table->id();
            foreach (['studentNum', 'company_name', 'company_address', 'nature_of_bus', 'assigned_department',
                'student_role', 'start_date', 'finish_date', 'contact_name', 'report_time', 'contact_position',
                'contact_number', 'status'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
        OJTInformation::create(['studentNum' => 'QA-10', 'company_name' => 'Original Company',
            'start_date' => '2026-10-01', 'finish_date' => '2027-01-31', 'status' => 'Pending']);
        Schema::create('file_requirements', function (Blueprint $table) {
            $table->id();
            foreach (['fileName', 'file', 'adviser', 'uploadedBy', 'denial_reason'] as $column) {
                $table->string($column)->nullable();
            }
            $table->integer('uploader_user_id');
            $table->integer('status');
            $table->timestamps();
        });
        FileRequirement::create(['fileName' => 'Resume', 'file' => 'test.pdf', 'status' => 0,
            'uploadedBy' => 'Student Alpha', 'uploader_user_id' => 10, 'adviser' => 'Assigned Professor']);
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            foreach (['user_id', 'user_name', 'user_role', 'affected_user_id', 'affected_name',
                'module', 'action', 'description', 'ip_address'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('integrity_testing');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public static function invalidDates(): array
    {
        return [
            'reversed' => ['2027-02-01', '2026-10-01'],
            'malformed start' => ['not-a-date', '2027-01-31'],
            'invalid calendar day' => ['2026-02-30', '2027-01-31'],
            'malformed finish' => ['2026-10-01', 'not-a-date'],
        ];
    }

    #[DataProvider('invalidDates')]
    public function test_invalid_dates_leave_original_record_unchanged(string $start, string $finish): void
    {
        Session::put('loginId', 10);
        $before = OJTInformation::first()->getAttributes();
        try {
            app(StudentController::class)->ojt_edit(Request::create('/', 'PUT', [
                'start_date' => $start, 'finish_date' => $finish,
            ]), 'QA-10');
            $this->fail('Invalid dates must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertNotEmpty($exception->errors());
        }
        $this->assertSame($before, OJTInformation::first()->getAttributes());
    }

    public function test_valid_equal_dates_save_only_to_the_logged_in_student(): void
    {
        Session::put('loginId', 10);
        app(StudentController::class)->ojt_edit(Request::create('/', 'PUT', [
            'company_name' => 'Updated Company', 'start_date' => '2026-10-01', 'finish_date' => '2026-10-01',
        ]), 'FORGED-STUDENT');
        $this->assertDatabaseHas('o_j_t_information', ['studentNum' => 'QA-10',
            'company_name' => 'Updated Company', 'finish_date' => '2026-10-01']);
        $this->assertDatabaseCount('o_j_t_information', 1);
    }

    public function test_unknown_status_is_rejected_without_mutation(): void
    {
        try {
            app(StudentController::class)->update(Request::create('/', 'PUT', ['status' => 'UNRECOGNIZED_STATUS']), 'QA-10');
            $this->fail('Unknown status must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }
        $this->assertSame('Pending', OJTInformation::first()->status);
    }

    public function test_every_status_offered_by_the_coordinator_form_is_accepted(): void
    {
        Session::put('loginId', 1);
        foreach (['Pending', 'Approved and For Notary', 'With Revision'] as $status) {
            app(StudentController::class)->update(Request::create('/', 'PUT', ['status' => $status]), 'QA-10');
            $this->assertSame($status, OJTInformation::first()->status);
        }
    }

    public static function unauthorizedReviewers(): array
    {
        return ['unrelated professor' => [3, 403], 'student' => [10, 403], 'guest' => [null, 401]];
    }

    #[DataProvider('unauthorizedReviewers')]
    public function test_unauthorized_reviewers_cannot_approve_or_deny(?int $actor, int $status): void
    {
        Session::put('loginId', $actor);
        foreach (['updateApproveStatus', 'updateDeniedStatus'] as $method) {
            try {
                app(PassDocuController::class)->{$method}(Request::create('/', 'POST', ['reason' => 'Test denial']), 1);
                $this->fail('Unauthorized review must be rejected.');
            } catch (HttpException $exception) {
                $this->assertSame($status, $exception->getStatusCode());
            }
            $this->assertSame(0, FileRequirement::find(1)->status);
            $this->assertNull(FileRequirement::find(1)->denial_reason);
        }
        Mail::assertNothingSent();
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_assigned_professor_and_coordinator_can_review(): void
    {
        foreach ([2, 1] as $actor) {
            Session::put('loginId', $actor);
            app(PassDocuController::class)->updateApproveStatus(Request::create('/', 'POST'), 1);
            $this->assertSame(1, FileRequirement::find(1)->status);
            app(PassDocuController::class)->updateDeniedStatus(Request::create('/', 'POST', ['reason' => 'Missing signature']), 1);
            $this->assertSame(2, FileRequirement::find(1)->status);
            $this->assertSame('Missing signature', FileRequirement::find(1)->denial_reason);
        }
    }

    public function test_phase_unlock_requires_approved_basics_and_moa_or_inhouse_waiver(): void
    {
        $names = ['Resume', 'Medical Clearance', 'Good Moral', 'Consent Form', 'Endorsement Letter', 'Acceptance Letter', 'Notarized MOA'];
        foreach (array_slice($names, 1) as $name) {
            FileRequirement::create(['fileName' => $name, 'status' => 0, 'uploader_user_id' => 10]);
        }
        $controller = app(PassDocuController::class);
        $method = new \ReflectionMethod($controller, 'buildRequirementPhaseState');
        $student = new Student(['is_inhouse_ojt' => false]);
        $state = fn () => $method->invoke($controller, User::find(10), $student, null);
        $this->assertFalse($state()['otherRequirementsUnlocked']);
        FileRequirement::query()->update(['status' => 1]);
        $this->assertTrue($state()['otherRequirementsUnlocked']);
        FileRequirement::where('fileName', 'Medical Clearance')->update(['status' => 2]);
        $this->assertFalse($state()['otherRequirementsUnlocked']);
        FileRequirement::where('fileName', 'Medical Clearance')->update(['status' => 1]);
        FileRequirement::where('fileName', 'Notarized MOA')->delete();
        $this->assertFalse($state()['otherRequirementsUnlocked']);
        $student->is_inhouse_ojt = true;
        $this->assertTrue($state()['otherRequirementsUnlocked']);
        FileRequirement::where('fileName', 'Resume')->update(['status' => 0]);
        $this->assertFalse($state()['otherRequirementsUnlocked']);
    }

    public function test_http_review_routes_enforce_adviser_scope(): void
    {
        $this->withSession(['loginId' => 3]);
        $this->withCookie(config('session.cookie'), Session::getId());
        Cache::put('active_session_id:3', Session::getId());
        $this->post('/update/approve/status/1', [], ['Accept' => 'application/json'])->assertForbidden();
        $this->post('/update/denied/status/1', ['reason' => 'Wrong adviser'], ['Accept' => 'application/json'])->assertForbidden();
        $this->assertSame(0, FileRequirement::find(1)->status);
        $this->withSession(['loginId' => 2]);
        Session::save();
        Cache::put('active_session_id:2', Session::getId());
        $this->post('/update/approve/status/1')->assertRedirect();
        $this->assertSame(1, FileRequirement::find(1)->status);
    }

    public function test_http_edit_route_rejects_reversed_dates(): void
    {
        $this->withSession(['loginId' => 10]);
        $this->withCookie(config('session.cookie'), Session::getId());
        Cache::put('active_session_id:10', Session::getId());
        $this->withCredentials()->putJson('/student/ojtEdit/QA-10', ['start_date' => '2027-02-01', 'finish_date' => '2026-10-01'])
            ->assertUnprocessable()->assertJsonValidationErrors('finish_date');
        $this->assertSame('2027-01-31', OJTInformation::first()->finish_date);
    }

    public function test_report_table_keeps_students_with_missing_placement_details(): void
    {
        $source = file_get_contents(resource_path('views/ojtCoordinator/reportsT.blade.php'));
        preg_match('/<tbody>(.*?)<\/tbody>/s', $source, $matches);
        $studentData = [];
        foreach (['Alpha', 'Beta', 'Missing'] as $name) {
            $studentData[] = ['student' => (object) ['full_name' => $name, 'course' => 'IT', 'year_and_section' => '3-1'],
                'ojt' => $name === 'Alpha' ? OJTInformation::first() : null];
        }
        $html = Blade::render($matches[1], ['studentData' => $studentData]);
        $this->assertSame(3, substr_count($html, '<tr '));
        foreach (['Alpha', 'Beta', 'Missing', 'Original Company', 'Not provided'] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
    }
}
