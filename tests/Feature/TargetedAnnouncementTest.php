<?php

namespace Tests\Feature;

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\TargetedAnnouncementController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Announcements;
use App\Models\User;
use App\Services\AnnouncementAudienceService;
use App\Services\AnnouncementEmailService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TargetedAnnouncementTest extends TestCase
{
    private string $originalConnection;
    private AnnouncementAudienceService $audiences;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config()->set('database.connections.announcement_testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]);
        config()->set('database.default', 'announcement_testing');
        DB::purge('announcement_testing');
        $this->createTables();
        $this->seedAudience();
        config()->set('announcement_email.enabled', true);
        config()->set('announcement_email.interval_ms', 0);
        config()->set('app.url', 'https://internconnect.example.test');
        $this->audiences = app(AnnouncementAudienceService::class);
        Session::put('loginId', 1);
    }

    protected function tearDown(): void
    {
        DB::purge('announcement_testing');
        config()->set('database.default', $this->originalConnection);
        parent::tearDown();
    }

    public function test_only_ojt_professors_in_the_selected_period_and_program_qualify(): void
    {
        $this->assertSame([2], $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
        $this->assertSame([3, 2], $this->audiences->eligibleProfessors($this->filters(['programs' => []]))->pluck('id')->all());
    }

    public function test_selected_professors_never_fall_back_to_everyone(): void
    {
        $this->expectException(ValidationException::class);
        $this->audiences->resolve($this->filters(['audience' => 'professors', 'professor_mode' => 'selected', 'professor_ids' => []]));
    }

    public function test_forged_or_non_ojt_professor_ids_are_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->audiences->resolve($this->filters(['audience' => 'both', 'professor_mode' => 'selected', 'professor_ids' => [4]]));
    }

    public function test_both_includes_filtered_students_and_only_selected_professors_once(): void
    {
        $recipients = $this->audiences->resolve($this->filters(['audience' => 'both', 'programs' => [], 'professor_mode' => 'selected', 'professor_ids' => [2]]));
        $this->assertSame([2, 10, 11], $recipients['ids']);
        $this->assertSame([2], $recipients['professors']->pluck('id')->all());
    }

    public function test_coordinator_can_designate_and_remove_an_ojt_professor_without_a_class(): void
    {
        $controller = app(TargetedAnnouncementController::class);
        $controller->designate(Request::create('/', 'POST', [
            'user_id' => 4, 'course' => 'BSIT', 'school_year' => '2026-2027', 'semester' => '',
        ]));
        $this->assertSame([4, 2], $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
        $id = DB::table('ojt_professor_designations')->value('id');
        $controller->removeDesignation($id);
        $this->assertSame([2], $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
    }

    public function test_old_designations_do_not_qualify_for_the_current_period(): void
    {
        DB::table('ojt_professor_designations')->insert([
            'user_id' => 4, 'course' => 'BSIT', 'school_year' => '2025-2026', 'semester' => '',
        ]);
        $this->assertSame([2], $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
    }

    public function test_semester_aliases_work_and_unlinked_students_are_not_guessed(): void
    {
        DB::table('classes')->where('id', 20)->update(['semester' => '1st Sem']);
        $this->assertSame([2], $this->audiences->eligibleProfessors($this->filters(['semester' => '1st Semester']))->pluck('id')->all());
        $this->assertSame([10], $this->audiences->students($this->filters(['semester' => '1st Semester']))->pluck('id')->all());
        $this->assertCount(0, $this->audiences->students($this->filters(['semester' => '2nd Semester'])));
    }

    public function test_a_professor_with_only_current_ojt_students_is_eligible(): void
    {
        DB::table('students')->where('user_id', 10)->update(['adviser_name' => 'Faculty Only', 'class_id' => null]);
        $this->assertContains(4, $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
    }

    public function test_ambiguous_names_do_not_silently_target_multiple_faculty(): void
    {
        DB::table('users')->where('id', 4)->update(['full_name' => 'Professor IT']);
        $this->assertCount(0, $this->audiences->eligibleProfessors($this->filters()));
    }

    public function test_case_variations_of_duplicate_faculty_names_are_also_ambiguous(): void
    {
        DB::table('users')->where('id', 4)->update(['full_name' => 'professor it']);
        $this->assertCount(0, $this->audiences->eligibleProfessors($this->filters()));
    }

    public function test_inactive_accounts_are_not_recipients(): void
    {
        DB::table('users')->where('id', 2)->update(['status' => 'Inactive']);
        DB::table('users')->where('id', 10)->update(['status' => '0']);
        $this->assertCount(0, $this->audiences->eligibleProfessors($this->filters()));
        $this->assertCount(0, $this->audiences->students($this->filters()));
    }

    public function test_legacy_professor_status_zero_is_valid_without_treating_inactive_students_as_active(): void
    {
        DB::table('users')->whereIn('id', [2, 4, 10])->update(['status' => '0']);
        $this->assertSame([2], $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
        $this->assertCount(0, $this->audiences->students($this->filters()));
        $this->assertContains(4, $this->audiences->formOptions()['faculty']->pluck('id')->all());
        app(TargetedAnnouncementController::class)->designate(Request::create('/', 'POST', [
            'user_id' => 4, 'course' => 'BSIT', 'school_year' => '2026-2027', 'semester' => '',
        ]));
        $this->assertSame([4, 2], $this->audiences->eligibleProfessors($this->filters())->pluck('id')->all());
    }

    public function test_empty_audiences_are_rejected_instead_of_published(): void
    {
        $this->expectException(ValidationException::class);
        $this->audiences->resolve($this->filters(['school_year' => '2024-2025']));
    }

    public function test_publishing_saves_recipients_and_a_retry_does_not_duplicate(): void
    {
        $payload = $this->preview(['audience' => 'both']);
        $controller = app(TargetedAnnouncementController::class);
        $controller->publish(Request::create('/', 'POST', $payload));
        $controller->publish(Request::create('/', 'POST', $payload));
        $this->assertSame(1, Announcements::count());
        $this->assertSame([2, 10], DB::table('announcement_recipients')->orderBy('user_id')->pluck('user_id')->all());
        $this->assertSame('Students & OJT Professors', Announcements::first()->audience_label);
        $this->assertSame(1, DB::table('audit_logs')->where('action', 'Create')->count());
    }

    public function test_later_assignments_do_not_expand_existing_audiences(): void
    {
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $this->preview()));
        DB::table('students')->where('user_id', 11)->update(['course' => 'BSIT']);
        $this->assertCount(1, $this->audiences->visibleTo(User::find(10))->get());
        $this->assertCount(0, $this->audiences->visibleTo(User::find(11))->get());
        $this->assertCount(0, $this->audiences->visibleTo(User::find(2))->get());
    }

    public function test_changing_filters_after_preview_requires_confirmation_again(): void
    {
        $payload = $this->preview();
        $payload['programs'] = [];
        $this->expectException(ValidationException::class);
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $payload));
    }

    public function test_changing_assignments_after_preview_requires_confirmation_again(): void
    {
        $payload = $this->preview();
        DB::table('students')->where('user_id', 11)->update(['course' => 'BSIT']);
        $this->expectException(ValidationException::class);
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $payload));
    }

    public function test_legacy_student_announcements_are_not_exposed_to_professors(): void
    {
        $announcement = new Announcements();
        $announcement->announcer_user_id = 1;
        $announcement->announcer = 'Coordinator';
        $announcement->audience = 'all_students';
        $announcement->title = 'Legacy';
        $announcement->save();
        $this->assertSame(['Legacy'], $this->audiences->visibleTo(User::find(10))->pluck('title')->all());
        $this->assertCount(0, $this->audiences->visibleTo(User::find(2))->get());
    }

    public function test_professor_class_announcements_still_use_the_owned_class(): void
    {
        Session::put('loginId', 2);
        app(AnnouncementController::class)->announcement(Request::create('/', 'POST', [
            'title' => 'Class only', 'content' => 'Hello', 'course' => 'BSIT', 'room' => 'IT-1',
            'audience' => 'both', 'professor_ids' => [4],
        ]));
        $announcement = Announcements::firstOrFail();
        $this->assertSame('class', $announcement->audience);
        $this->assertSame('BSIT', $announcement->target_course);
        $this->assertNull($announcement->targeting);
        $this->assertCount(0, $this->audiences->visibleTo(User::find(11))->get());
    }

    public function test_coordinator_text_edits_preserve_the_saved_audience(): void
    {
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $this->preview()));
        $announcement = Announcements::firstOrFail();
        $targeting = $announcement->targeting;
        app(AnnouncementController::class)->update(Request::create('/', 'PUT', [
            'title' => 'Updated', 'content' => 'Updated message', 'audience' => 'both',
        ]), $announcement->id);
        $this->assertSame($targeting, $announcement->fresh()->targeting);
        $this->assertSame([10], DB::table('announcement_recipients')->pluck('user_id')->all());
    }

    public function test_students_and_professors_cannot_access_coordinator_targeting_endpoints(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        foreach ([2, 10] as $userId) {
            foreach (['/announcements/audience/professors', '/announcements/audience/preview', '/announcements/ojt-professors'] as $path) {
                $this->withSession(['loginId' => $userId])->postJson($path, [])->assertForbidden();
            }
            $this->withSession(['loginId' => $userId])->deleteJson('/announcements/ojt-professors/1')->assertForbidden();
        }
    }

    public function test_invalid_audiences_and_programs_are_rejected_by_the_endpoint(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->withSession(['loginId' => 1])->postJson('/announcements/audience/preview', [
            'audience' => 'all_faculty', 'programs' => ['Unknown'], 'professor_mode' => 'all',
        ])->assertUnprocessable()->assertJsonValidationErrors(['audience', 'programs.0']);
    }

    public function test_the_migration_does_not_modify_existing_announcements(): void
    {
        $migration = require database_path('migrations/2026_10_31_000001_add_targeted_announcements.php');
        $migration->down();
        DB::table('announcements')->insert(['announcer' => 'Coordinator', 'title' => 'Keep this announcement', 'content' => 'Keep this content', 'audience' => 'all_students']);
        $migration->up();
        $this->assertTrue(Schema::hasTable('announcement_recipients'));
        $this->assertTrue(Schema::hasTable('ojt_professor_designations'));
        $this->assertDatabaseHas('announcements', ['title' => 'Keep this announcement', 'content' => 'Keep this content', 'targeting' => null]);
        $this->assertSame(1, Announcements::count());
    }

    public function test_form_partials_render_the_selection_and_designation_controls(): void
    {
        $options = $this->audiences->formOptions();
        $form = view('partials.announcement-audience', ['announcementOptions' => $options])->render();
        $this->assertStringContainsString('Select specific OJT professors', $form);
        $this->assertStringContainsString('All Programs', $form);
        $this->assertStringContainsString('name="programs[]"', $form);
        $designations = view('partials.announcement-designations', ['announcementOptions' => $options])->render();
        $this->assertStringContainsString('Faculty Only', $designations);
        $this->assertStringContainsString('name="user_id"', $designations);
    }

    public function test_the_http_publish_route_uses_the_confirmed_recipient_snapshot(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->withSession(['loginId' => 1])->post('/announcements', $this->preview(['audience' => 'professors']))->assertRedirect();
        $this->assertSame([2], DB::table('announcement_recipients')->pluck('user_id')->all());
    }

    public function test_coordinator_cannot_publish_without_previewing(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $this->withSession(['loginId' => 1])->postJson('/announcements', array_merge($this->filters(), [
            'title' => 'Unconfirmed', 'content' => 'Message',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['confirmation', 'publication_key']);
        $this->assertSame(0, Announcements::count());
    }

    public function test_coordinator_cannot_modify_another_authors_announcement(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        DB::table('announcements')->insert(['id' => 1, 'announcer_user_id' => 2, 'announcer' => 'Professor IT', 'title' => 'Owned class message', 'audience' => 'class']);
        $this->withSession(['loginId' => 1])->put('/announcements/1', ['title' => 'Changed', 'content' => 'Changed'])->assertNotFound();
        $this->withSession(['loginId' => 1])->delete('/announcements/1')->assertNotFound();
        $this->assertDatabaseHas('announcements', ['id' => 1, 'title' => 'Owned class message']);
    }

    private function filters(array $overrides = []): array
    {
        return $this->audiences->normalize(array_merge([
            'audience' => 'students', 'programs' => ['BSIT'], 'school_year' => '2026-2027',
            'semester' => '', 'professor_mode' => 'all', 'professor_ids' => [],
        ], $overrides));
    }

    private function preview(array $overrides = []): array
    {
        $filters = $this->filters($overrides);
        $response = app(TargetedAnnouncementController::class)->preview(Request::create('/', 'POST', $filters))->getData(true);
        return array_merge($filters, ['title' => 'Orientation', 'content' => 'Please attend.',
            'confirmation' => $response['confirmation'], 'publication_key' => $response['publication_key']]);
    }

    private function seedAudience(): void
    {
        foreach ([1 => ['Coordinator', 1], 2 => ['Professor IT', 2], 3 => ['Professor ECE', 2], 4 => ['Faculty Only', 2], 5 => ['Archived Professor', 2], 10 => ['Student IT', 0], 11 => ['Student ECE', 0]] as $id => [$name, $role]) {
            DB::table('users')->insert(['id' => $id, 'full_name' => $name, 'role' => $role, 'status' => $role === 0 ? '1' : 'Active', 'email' => 'user'.$id.'@example.test']);
        }
        DB::table('courses')->insert([['course' => 'BSIT'], ['course' => 'BSECE']]);
        foreach ([20 => ['Professor IT', 'BSIT', 'IT-1', null], 21 => ['Professor ECE', 'BSECE', 'ECE-1', null], 22 => ['Archived Professor', 'BSIT', 'OLD-1', now()]] as $id => [$adviser, $course, $room, $archived]) {
            DB::table('classes')->insert(['id' => $id, 'adviser_name' => $adviser, 'course' => $course, 'room' => $room,
                'school_year_start' => 2026, 'school_year_end' => 2027, 'semester' => '1st Semester', 'archived_at' => $archived]);
        }
        DB::table('students')->insert([
            ['user_id' => 10, 'adviser_name' => 'Professor IT', 'course' => 'BSIT', 'class_id' => 20, 'school_year_start' => 2026, 'school_year_end' => 2027],
            ['user_id' => 11, 'adviser_name' => 'Professor ECE', 'course' => 'BSECE', 'class_id' => 21, 'school_year_start' => 2026, 'school_year_end' => 2027],
        ]);
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('full_name'); $table->string('role'); $table->string('status'); $table->string('email')->nullable();
        });
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); $table->string('course');
        });
        Schema::create('classes', function (Blueprint $table) {
            $table->id(); $table->string('adviser_name'); $table->string('course'); $table->string('room');
            $table->integer('school_year_start'); $table->integer('school_year_end'); $table->string('semester'); $table->timestamp('archived_at')->nullable();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('adviser_name'); $table->string('course');
            $table->unsignedBigInteger('class_id')->nullable(); $table->integer('school_year_start'); $table->integer('school_year_end');
        });
        Schema::create('announcements', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('announcer_user_id')->nullable(); $table->string('announcer');
            $table->string('title')->nullable(); $table->text('content')->nullable(); $table->string('audience')->nullable();
            $table->string('target_course')->nullable(); $table->string('target_room')->nullable(); $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable(); $table->string('user_name')->nullable(); $table->string('user_role')->nullable();
            $table->unsignedBigInteger('affected_user_id')->nullable(); $table->string('affected_name')->nullable();
            $table->string('action'); $table->string('module'); $table->text('description')->nullable(); $table->string('ip_address')->nullable(); $table->timestamps();
        });
        (require database_path('migrations/2026_10_31_000001_add_targeted_announcements.php'))->up();
        (require database_path('migrations/2026_10_31_000002_add_announcement_email_outbox.php'))->up();
    }

    public function test_email_opt_out_never_creates_outbox_records(): void
    {
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $this->preview()));
        $this->assertSame(0, DB::table('announcement_email_deliveries')->count());
        $this->assertNull(Announcements::first()->email_payload);
    }

    public function test_email_preview_counts_only_selected_recipients_and_skips_invalid_addresses(): void
    {
        DB::table('users')->where('id', 10)->update(['email' => 'not-an-email']);
        $response = app(TargetedAnnouncementController::class)->preview(Request::create('/', 'POST', $this->filters(['audience' => 'both', 'send_email' => true])))->getData(true);
        $this->assertSame(1, $response['email']['valid']);
        $this->assertSame(1, $response['email']['skipped']);
        $this->assertTrue($response['email']['capture']);
        $announcement = $this->publishWithEmail(['audience' => 'both']);
        $this->assertDatabaseHas('announcement_email_deliveries', ['announcement_id' => $announcement->id, 'user_id' => 10, 'status' => 'skipped']);
        $this->assertDatabaseHas('announcement_email_deliveries', ['announcement_id' => $announcement->id, 'user_id' => 2, 'status' => 'queued']);
    }

    public function test_email_publish_is_queued_and_duplicate_submission_cannot_duplicate_email(): void
    {
        Mail::fake();
        $payload = $this->preview(['send_email' => true]);
        $controller = app(TargetedAnnouncementController::class);
        $controller->publish(Request::create('/', 'POST', $payload));
        $controller->publish(Request::create('/', 'POST', $payload));
        $this->assertSame(1, DB::table('announcement_email_deliveries')->count());
        $this->assertDatabaseHas('announcement_email_deliveries', ['user_id' => 10, 'status' => 'queued', 'attempts' => 0]);
        Mail::assertNothingSent();
    }

    public function test_email_cannot_be_enabled_after_preview_without_confirming_again(): void
    {
        $payload = $this->preview();
        $payload['send_email'] = true;
        $this->expectException(ValidationException::class);
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $payload));
    }

    public function test_changed_email_addresses_require_another_preview(): void
    {
        $payload = $this->preview(['send_email' => true]);
        DB::table('users')->where('id', 10)->update(['email' => 'changed@example.test']);
        $this->expectException(ValidationException::class);
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $payload));
    }

    public function test_email_with_no_valid_addresses_is_rejected(): void
    {
        DB::table('users')->where('id', 10)->update(['email' => null]);
        $this->expectException(ValidationException::class);
        $this->preview(['send_email' => true]);
    }

    public function test_disabled_email_does_not_prevent_an_in_app_only_announcement(): void
    {
        config()->set('announcement_email.enabled', false);
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $this->preview()));
        $this->assertSame(1, Announcements::count());
        $this->expectException(ValidationException::class);
        $this->preview(['send_email' => true]);
    }

    public function test_capture_sends_one_private_message_per_recipient_and_does_not_resend(): void
    {
        $this->publishWithEmail(['audience' => 'both']);
        $emails = app(AnnouncementEmailService::class);
        foreach (DB::table('announcement_email_deliveries')->pluck('id') as $id) {
            $this->assertSame('captured', $emails->deliver($id));
            $this->assertSame('ignored', $emails->deliver($id));
        }
        $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(2, $messages);
        foreach ($messages as $sent) {
            $message = $sent->getOriginalMessage();
            $this->assertCount(1, $message->getTo());
            $this->assertCount(0, $message->getCc());
            $this->assertCount(0, $message->getBcc());
            $path = $message->getTo()[0]->getAddress() === 'user2@example.test' ? '/professor/home' : '/student/home';
            $this->assertStringContainsString('https://internconnect.example.test'.$path, $message->getHtmlBody());
        }
    }

    public function test_editing_a_message_does_not_change_pending_email_content_or_address(): void
    {
        $announcement = $this->publishWithEmail();
        DB::table('announcements')->where('id', $announcement->id)->update(['title' => 'Edited title', 'content' => 'Edited content']);
        DB::table('users')->where('id', 10)->update(['email' => 'new@example.test']);
        $id = DB::table('announcement_email_deliveries')->value('id');
        $this->assertSame('captured', app(AnnouncementEmailService::class)->deliver($id));
        $message = Mail::mailer('array')->getSymfonyTransport()->messages()->first()->getOriginalMessage();
        $this->assertSame('[InternConnect] Orientation', $message->getSubject());
        $this->assertSame('user10@example.test', $message->getTo()[0]->getAddress());
        $this->assertStringContainsString('Please attend.', $message->getHtmlBody());
        $this->assertStringNotContainsString('Edited content', $message->getHtmlBody());
    }

    public function test_configuration_failure_preserves_the_announcement_and_can_be_retried(): void
    {
        $announcement = $this->publishWithEmail();
        $id = DB::table('announcement_email_deliveries')->value('id');
        config()->set('mail.mailers.array.transport', 'unsupported');
        $emails = app(AnnouncementEmailService::class);
        $this->assertSame('failed', $emails->deliver($id));
        $this->assertCount(1, $this->audiences->visibleTo(User::find(10))->get());
        $this->assertSame(1, $emails->retryFailed($announcement));
        config()->set('mail.mailers.array.transport', 'array');
        $this->assertSame('captured', $emails->deliver($id));
        $this->assertSame(0, $emails->retryFailed($announcement));
        $this->assertDatabaseHas('announcement_email_deliveries', ['id' => $id, 'attempts' => 2, 'status' => 'captured']);
    }

    public function test_transport_errors_are_uncertain_and_not_automatically_retried(): void
    {
        $announcement = $this->publishWithEmail();
        $transport = \Mockery::mock(\Symfony\Component\Mailer\Transport\TransportInterface::class);
        $transport->shouldReceive('send')->once()->andThrow(new \RuntimeException('Connection lost'));
        Mail::mailer('array')->setSymfonyTransport($transport);
        $id = DB::table('announcement_email_deliveries')->value('id');
        $emails = app(AnnouncementEmailService::class);
        $this->assertSame('uncertain', $emails->deliver($id));
        $this->assertSame(0, $emails->retryFailed($announcement));
        $this->assertSame('ignored', $emails->deliver($id));
        $this->assertDatabaseHas('announcement_email_deliveries', ['id' => $id, 'status' => 'uncertain']);
    }

    public function test_interrupted_workers_require_review_instead_of_resending(): void
    {
        $announcement = $this->publishWithEmail();
        DB::table('announcement_email_deliveries')->update(['status' => 'sending', 'processing_started_at' => now()->subMinutes(16)]);
        $emails = app(AnnouncementEmailService::class);
        $this->assertSame(1, $emails->markInterrupted());
        $this->assertSame(0, $emails->retryFailed($announcement));
        $this->assertDatabaseHas('announcement_email_deliveries', ['status' => 'uncertain', 'last_error' => 'worker_interrupted']);
    }

    public function test_failed_emails_stop_after_three_attempts(): void
    {
        $announcement = $this->publishWithEmail();
        DB::table('announcement_email_deliveries')->update(['status' => 'failed', 'attempts' => 3]);
        $this->assertSame(0, app(AnnouncementEmailService::class)->retryFailed($announcement));
    }

    public function test_non_owners_and_non_coordinators_cannot_retry_emails(): void
    {
        $this->withoutMiddleware([AuthMiddleware::class, RoleMiddleware::class]);
        $announcement = $this->publishWithEmail();
        foreach ([2, 10] as $userId) {
            $this->withSession(['loginId' => $userId])->postJson('/announcements/'.$announcement->id.'/retry-emails')->assertForbidden();
        }
        DB::table('announcements')->where('id', $announcement->id)->update(['announcer_user_id' => 2]);
        $this->withSession(['loginId' => 1])->postJson('/announcements/'.$announcement->id.'/retry-emails')->assertNotFound();
    }

    public function test_deleting_an_announcement_cancels_its_pending_emails(): void
    {
        $announcement = $this->publishWithEmail();
        $announcement->delete();
        $this->assertSame(0, DB::table('announcement_email_deliveries')->count());
    }

    public function test_deactivated_student_accounts_are_skipped_before_sending(): void
    {
        $this->publishWithEmail();
        DB::table('users')->where('id', 10)->update(['status' => '0']);
        $id = DB::table('announcement_email_deliveries')->value('id');
        $this->assertSame('skipped', app(AnnouncementEmailService::class)->deliver($id));
        $this->assertDatabaseHas('announcement_email_deliveries', ['id' => $id, 'last_error' => 'recipient_unavailable']);
    }

    public function test_retry_endpoint_requeues_only_failed_records(): void
    {
        $announcement = $this->publishWithEmail(['audience' => 'both']);
        DB::table('announcement_email_deliveries')->where('user_id', 2)->update(['status' => 'captured', 'attempts' => 1]);
        DB::table('announcement_email_deliveries')->where('user_id', 10)->update(['status' => 'failed', 'attempts' => 1]);
        app(TargetedAnnouncementController::class)->retryEmails($announcement->id);
        app(TargetedAnnouncementController::class)->retryEmails($announcement->id);
        $this->assertDatabaseHas('announcement_email_deliveries', ['user_id' => 2, 'status' => 'captured', 'attempts' => 1]);
        $this->assertDatabaseHas('announcement_email_deliveries', ['user_id' => 10, 'status' => 'queued', 'attempts' => 1]);
        $this->assertSame(2, DB::table('announcement_email_deliveries')->count());
    }

    public function test_command_processes_only_its_limit_and_captures_without_real_email(): void
    {
        $this->publishWithEmail(['audience' => 'both']);
        $this->artisan('announcements:send-emails', ['--limit' => 1])->assertSuccessful();
        $this->assertSame(1, DB::table('announcement_email_deliveries')->where('status', 'captured')->count());
        $this->assertSame(1, DB::table('announcement_email_deliveries')->where('status', 'queued')->count());
        $this->artisan('announcements:send-emails', ['--limit' => 0])->assertFailed();
    }

    public function test_local_environment_forces_capture_even_if_smtp_is_configured(): void
    {
        $this->app['env'] = 'local';
        config()->set('announcement_email.mailer', 'smtp');
        $this->assertSame('announcement_log', app(AnnouncementEmailService::class)->mailer());
        $this->assertTrue(app(AnnouncementEmailService::class)->captureOnly());
    }

    public function test_live_transport_acceptance_records_sent_without_using_a_network_transport(): void
    {
        $announcement = $this->publishWithEmail();
        $this->app['env'] = 'production';
        config()->set('announcement_email.mailer', 'smtp');
        $transport = new \Illuminate\Mail\Transport\ArrayTransport();
        Mail::mailer('smtp')->setSymfonyTransport($transport);
        $id = DB::table('announcement_email_deliveries')->value('id');
        $emails = app(AnnouncementEmailService::class);
        $this->assertSame('sent', $emails->deliver($id));
        $this->assertSame('ignored', $emails->deliver($id));
        $this->assertSame(0, $emails->retryFailed($announcement));
        $this->assertCount(1, $transport->messages());
        $this->assertDatabaseHas('announcement_email_deliveries', ['id' => $id, 'status' => 'sent']);
    }

    public function test_disabling_email_pauses_existing_outbox_records(): void
    {
        $this->publishWithEmail();
        config()->set('announcement_email.enabled', false);
        $this->artisan('announcements:send-emails', ['--limit' => 1])->assertSuccessful();
        $this->assertDatabaseHas('announcement_email_deliveries', ['status' => 'queued', 'attempts' => 0]);
    }

    public function test_dashboard_summaries_report_outbox_status_counts(): void
    {
        $announcement = $this->publishWithEmail(['audience' => 'both']);
        DB::table('announcement_email_deliveries')->where('user_id', 2)->update(['status' => 'captured']);
        $announcements = collect([$announcement]);
        app(AnnouncementEmailService::class)->attachSummaries($announcements);
        $this->assertSame(1, $announcement->email_summary['queued']);
        $this->assertSame(1, $announcement->email_summary['captured']);
    }

    public function test_email_migration_preserves_old_announcements_without_retroactive_email(): void
    {
        DB::table('announcements')->insert(['title' => 'Existing message', 'content' => 'Keep this message', 'announcer' => 'Coordinator']);
        $migration = require database_path('migrations/2026_10_31_000002_add_announcement_email_outbox.php');
        $migration->down();
        $migration->up();
        $this->assertDatabaseHas('announcements', ['title' => 'Existing message', 'content' => 'Keep this message', 'email_payload' => null]);
        $this->assertSame(0, DB::table('announcement_email_deliveries')->count());
    }

    public function test_message_html_is_escaped(): void
    {
        $message = new \App\Mail\AnnouncementNotification([
            'title' => '<script>alert(1)</script>', 'content' => '<img src=x onerror=alert(1)>',
            'coordinator' => '<b>Someone</b>',
        ], 'https://internconnect.example.test/student/home');
        $html = $message->render();
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('&lt;img', $html);
    }

    private function publishWithEmail(array $overrides = []): Announcements
    {
        $payload = $this->preview(array_merge(['send_email' => true], $overrides));
        app(TargetedAnnouncementController::class)->publish(Request::create('/', 'POST', $payload));
        return Announcements::where('publication_key', $payload['publication_key'])->firstOrFail();
    }
}
