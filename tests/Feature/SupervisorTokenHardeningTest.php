<?php

namespace Tests\Feature;

use App\Http\Controllers\EvaluationController;
use App\Mail\SupervisorEvaluationCode;
use App\Services\SupervisorEmailVerification;
use App\Models\OjtEvaluation;
use App\Models\OjtEvaluationRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SupervisorTokenHardeningTest extends TestCase
{
    private string $originalConnection;
    protected OjtEvaluationRequest $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config(['database.connections.token_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ], 'database.default' => 'token_testing']);
        DB::purge('token_testing');
        config(['cache.default' => 'array']);
        Cache::flush();
        Session::flush();
        Carbon::setTestNow(Carbon::parse('2026-11-11 12:00:00'));
        Mail::fake();
        Http::preventStrayRequests();
        Session::forget('loginId');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
        });
        DB::table('users')->insert(['id' => 10, 'full_name' => 'Token Test Student']);
        foreach ([
            '2026_04_02_100000_create_ojt_evaluation_tables.php',
            '2026_07_16_000001_add_supervisor_confirmation_to_ojt_evaluations.php',
            '2026_07_16_000003_add_released_to_student_at_to_ojt_evaluations.php',
        ] as $migration) {
            (require database_path('migrations/' . $migration))->up();
        }
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            foreach (['user_id', 'user_name', 'user_role', 'affected_user_id', 'affected_name', 'module', 'action', 'description', 'ip_address'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
        $this->invitation = OjtEvaluationRequest::create([
            'student_id' => 10, 'template_id' => 1, 'student_num' => 'TEST-10',
            'student_name' => 'Token Test Student', 'supervisor_email' => 'supervisor@example.invalid',
            'token' => str_repeat('A', 64), 'token_expires_at' => now()->addDay(), 'status' => 'sent',
        ]);
        $verification = app(SupervisorEmailVerification::class);
        $verification->send($this->invitation, '127.0.0.1');
        $mail = Mail::sent(SupervisorEvaluationCode::class)->last();
        $verification->verify($this->invitation, $mail->code, '127.0.0.1');
        Mail::fake();
        Cache::flush();
    }

    protected function tearDown(): void
    {
        OjtEvaluation::flushEventListeners();
        Carbon::setTestNow();
        DB::purge('token_testing');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public static function rejectedStates(): array
    {
        return [
            'past deadline' => ['sent', -1],
            'exact deadline EXP-04' => ['sent', 0],
            'missing deadline EXP-05' => ['sent', null],
            'explicitly expired EXP-06' => ['expired', 3600],
            'cancelled' => ['cancelled', 3600],
            'unknown state' => ['unexpected', 3600],
        ];
    }

    #[DataProvider('rejectedStates')]
    public function test_invalid_states_cannot_open_review_or_submit(string $status, ?int $seconds): void
    {
        $this->invitation->update([
            'status' => $status, 'token_expires_at' => $seconds === null ? null : now()->addSeconds($seconds),
        ]);
        $controller = new EvaluationController;
        $view = $controller->showSupervisorForm($this->invitation->token)->getData();
        $this->assertTrue((bool) ($view['expired'] || $view['cancelled']));
        $review = $controller->reviewSupervisorForm($this->payload(), $this->invitation->token);
        $this->assertTrue($review->getSession()->has('error'));
        $submission = $controller->submitSupervisorForm($this->payload(), $this->invitation->token);
        $this->assertTrue($submission->getSession()->has('error'));
        $this->assertSame(0, OjtEvaluation::count());
    }

    public function test_submission_just_before_expiry_succeeds_and_replay_preserves_scores(): void
    {
        $this->invitation->update(['token_expires_at' => now()->addSecond()]);
        $controller = new EvaluationController;
        $controller->submitSupervisorForm($this->payload(5), $this->invitation->token);
        $replay = $controller->submitSupervisorForm($this->payload(1), $this->invitation->token);
        $this->assertTrue($replay->getSession()->has('error'));
        $this->assertSame('submitted', $this->invitation->fresh()->status);
        $this->assertSame(1, OjtEvaluation::count());
        $this->assertSame(5, OjtEvaluation::first()->responses[0]['score']);
    }

    public function test_interleaved_post_cannot_overwrite_the_first_evaluation(): void
    {
        $nestedResponse = null;
        OjtEvaluation::created(function () use (&$nestedResponse) {
            $nestedResponse = (new EvaluationController)->submitSupervisorForm($this->payload(1), $this->invitation->token);
        });
        (new EvaluationController)->submitSupervisorForm($this->payload(5), $this->invitation->token);
        $this->assertTrue($nestedResponse->getSession()->has('error'));
        $this->assertSame(1, OjtEvaluation::count());
        $this->assertSame(5, OjtEvaluation::first()->responses[0]['score']);
    }

    public function test_failed_persistence_rolls_back_token_consumption(): void
    {
        OjtEvaluation::creating(function () { throw new \RuntimeException('Simulated persistence failure'); });
        try {
            (new EvaluationController)->submitSupervisorForm($this->payload(), $this->invitation->token);
            $this->fail('Expected simulated persistence failure');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated persistence failure', $exception->getMessage());
        }
        $this->assertSame('sent', $this->invitation->fresh()->status);
        $this->assertNull($this->invitation->fresh()->submitted_at);
        $this->assertSame(0, OjtEvaluation::count());
    }

    public function test_existing_evaluation_blocks_overwrite_even_if_request_state_is_inconsistent(): void
    {
        OjtEvaluation::create([
            'request_id' => $this->invitation->id, 'template_id' => 1,
            'responses_json' => json_encode([['score' => 4]]), 'submitted_at' => now(),
        ]);
        (new EvaluationController)->submitSupervisorForm($this->payload(1), $this->invitation->token);
        $this->assertSame(1, OjtEvaluation::count());
        $this->assertSame(4, OjtEvaluation::first()->responses[0]['score']);
    }

    public function test_copied_link_cannot_view_student_details_without_email_verification(): void
    {
        app(SupervisorEmailVerification::class)->forget($this->invitation);
        $response = $this->get(route('evaluation.form.show', $this->invitation->token));
        $response->assertOk()->assertSeeText('Secure Supervisor Verification')
            ->assertDontSeeText('Token Test Student')->assertDontSee('name="rating_', false);
        Mail::assertNothingSent();
    }

    public function test_copied_link_cannot_review_or_submit_even_with_an_application_login(): void
    {
        app(SupervisorEmailVerification::class)->forget($this->invitation);
        Session::put('loginId', 10);
        $controller = new EvaluationController;
        $controller->reviewSupervisorForm($this->payload(), $this->invitation->token);
        $response = $controller->submitSupervisorForm($this->payload(), $this->invitation->token);
        $this->assertTrue($response->getSession()->has('error'));
        $this->assertSame(0, OjtEvaluation::count());
        $this->assertSame('sent', $this->invitation->fresh()->status);
    }

    public function test_code_is_sent_only_to_the_recorded_email_and_unlocks_the_form(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $verification->forget($this->invitation);
        $verification->send($this->invitation, '127.0.0.1');
        Mail::assertSent(SupervisorEvaluationCode::class, fn ($mail) => $mail->hasTo('supervisor@example.invalid'));
        $mail = Mail::sent(SupervisorEvaluationCode::class)->last();
        $verification->verify($this->invitation, $mail->code, '127.0.0.1');
        $this->assertTrue($verification->isVerified($this->invitation));
        $this->withCookie(config('session.cookie'), Session::getId())->get(route('evaluation.form.show', $this->invitation->token))
            ->assertOk()->assertSeeText('Token Test Student')->assertSee('name="rating_', false);
    }

    public function test_code_cannot_be_used_in_another_browser_session(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $verification->forget($this->invitation);
        $verification->send($this->invitation, '127.0.0.1');
        $code = Mail::sent(SupervisorEvaluationCode::class)->last()->code;
        Session::setId(str_repeat('b', 40));
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $verification->verify($this->invitation, $code, '127.0.0.1');
    }

    public function test_code_expires_at_ten_minutes(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $verification->forget($this->invitation);
        $verification->send($this->invitation, '127.0.0.1');
        $code = Mail::sent(SupervisorEvaluationCode::class)->last()->code;
        Carbon::setTestNow(now()->addMinutes(10));
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $verification->verify($this->invitation, $code, '127.0.0.1');
    }

    public function test_five_wrong_attempts_block_even_the_correct_code(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $verification->forget($this->invitation);
        $verification->send($this->invitation, '127.0.0.1');
        $code = Mail::sent(SupervisorEvaluationCode::class)->last()->code;
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                $verification->verify($this->invitation, '000000', '127.0.0.1');
                $this->fail('Incorrect code was accepted');
            } catch (\Illuminate\Validation\ValidationException $exception) {
                $this->assertFalse($verification->isVerified($this->invitation));
            }
        }
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $verification->verify($this->invitation, $code, '127.0.0.1');
    }

    public function test_resend_cooldown_prevents_duplicate_emails(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $verification->forget($this->invitation);
        $verification->send($this->invitation, '127.0.0.1');
        try {
            $verification->send($this->invitation, '127.0.0.1');
            $this->fail('Resend cooldown was not enforced');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            Mail::assertSent(SupervisorEvaluationCode::class, 1);
        }
    }

    public function test_verification_expires_and_is_invalidated_by_token_or_email_changes(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $this->assertTrue($verification->isVerified($this->invitation));
        $originalToken = $this->invitation->token;
        $this->invitation->token = str_repeat('B', 64);
        $this->assertFalse($verification->isVerified($this->invitation));
        $this->invitation->token = $originalToken;
        $this->invitation->supervisor_email = 'other@example.invalid';
        $this->assertFalse($verification->isVerified($this->invitation));
        $this->invitation->refresh();
        Carbon::setTestNow(now()->addMinutes(30));
        $this->assertFalse($verification->isVerified($this->invitation));
    }

    public function test_verification_code_is_single_use(): void
    {
        $verification = app(SupervisorEmailVerification::class);
        $verification->forget($this->invitation);
        $verification->send($this->invitation, '127.0.0.1');
        $code = Mail::sent(SupervisorEvaluationCode::class)->last()->code;
        $verification->verify($this->invitation, $code, '127.0.0.1');
        $verification->forget($this->invitation);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $verification->verify($this->invitation, $code, '127.0.0.1');
    }

    public function test_browser_can_request_and_verify_code_through_public_routes(): void
    {
        app(SupervisorEmailVerification::class)->forget($this->invitation);
        $this->withCookie(config('session.cookie'), Session::getId());
        $this->post(route('evaluation.verification.send', $this->invitation->token))
            ->assertRedirect(route('evaluation.form.show', $this->invitation->token));
        $code = Mail::sent(SupervisorEvaluationCode::class)->last()->code;
        $this->post(route('evaluation.verification.verify', $this->invitation->token), ['code' => $code])
            ->assertRedirect(route('evaluation.form.show', $this->invitation->token));
        $this->get(route('evaluation.form.show', $this->invitation->token))
            ->assertOk()->assertSee('Token Test Student');
    }

    public function test_invalid_code_is_not_flashed_to_old_input(): void
    {
        app(SupervisorEmailVerification::class)->forget($this->invitation);
        $this->withCookie(config('session.cookie'), Session::getId());
        $this->from(route('evaluation.form.show', $this->invitation->token))
            ->post(route('evaluation.verification.verify', $this->invitation->token), ['code' => '000000'])
            ->assertSessionHasErrors('code')->assertSessionMissing('_old_input.code');
    }

    private function payload(int $score = 5): Request
    {
        $data = ['supervisor_name' => 'Test Supervisor', 'supervisor_confirmation' => '1'];
        foreach ($this->invitation->template->items->where('input_type', 'rating') as $item) {
            $data['rating_' . $item->id] = $score;
        }
        return Request::create('/evaluation/form/' . $this->invitation->token, 'POST', $data);
    }
}
