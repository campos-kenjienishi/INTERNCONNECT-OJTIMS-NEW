<?php

namespace Tests\Feature;

use App\Mail\ForgotPassNotif;
use App\Models\User;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocalPasswordRecoveryTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config([
            'database.connections.password_recovery_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'database.default' => 'password_recovery_testing', 'app.url' => 'https://internconnect.example.test',
            'auth.passwords.users.expire' => 60, 'auth.passwords.users.throttle' => 60,
        ]);
        DB::purge('password_recovery_testing');
        Mail::fake();
        Http::preventStrayRequests();
        $maintenance = Mockery::mock(MaintenanceMode::class);
        $maintenance->shouldReceive('active')->andReturn(false);
        $this->app->instance(MaintenanceMode::class, $maintenance);

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('full_name'); $table->string('email')->unique(); $table->string('password');
            $table->string('role'); $table->boolean('has_local_password')->default(true); $table->string('idp_user_id')->nullable(); $table->rememberToken(); $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable(); $table->string('user_name')->nullable(); $table->string('user_role')->nullable();
            $table->unsignedBigInteger('affected_user_id')->nullable(); $table->string('affected_name')->nullable(); $table->string('action'); $table->string('module'); $table->text('description')->nullable(); $table->string('ip_address')->nullable(); $table->timestamps();
        });
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id(); $table->string('tokenable_type'); $table->unsignedBigInteger('tokenable_id'); $table->string('name'); $table->string('token'); $table->text('abilities')->nullable(); $table->timestamps();
        });
        (require database_path('migrations/2014_10_12_100000_create_password_reset_tokens_table.php'))->up();
        foreach ([1 => '0', 2 => '2', 3 => '1', 4 => '0'] as $id => $role) {
            DB::table('users')->insert([
                'id' => $id, 'full_name' => 'Password Test '.$id, 'email' => 'account'.$id.'@example.test',
                'password' => Hash::make('OriginalPassword!9'), 'has_local_password' => $id !== 4,
                'idp_user_id' => 'idp-account-'.$id, 'role' => $role, 'remember_token' => 'old-remember-token',
            ]);
        }
    }

    protected function tearDown(): void
    {
        DB::purge('password_recovery_testing');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public static function localAccounts(): array
    {
        return ['student' => [1], 'professor' => [2], 'coordinator' => [3]];
    }

    #[DataProvider('localAccounts')]
    public function test_local_accounts_receive_canonical_reset_links_with_hashed_tokens(int $id): void
    {
        $email = 'account'.$id.'@example.test';
        $this->post('/forgotPass', ['email' => $email])->assertRedirect('/forgot')->assertSessionHas('success');
        Mail::assertSent(ForgotPassNotif::class, function ($mail) use ($email) {
            $this->assertTrue($mail->hasTo($email));
            $this->assertStringStartsWith('https://internconnect.example.test/reset?', $mail->resetUrl);
            $query = $this->linkValues($mail->resetUrl);
            $this->assertSame($email, $query['email']);
            $this->assertSame(64, strlen($query['token']));
            $stored = DB::table('password_reset_tokens')->where('email', $email)->value('token');
            $this->assertNotSame($query['token'], $stored);
            $this->assertTrue(Hash::check($query['token'], $stored));
            $this->assertSame(60, $mail->expiresInMinutes);
            $html = $mail->render();
            $this->assertStringContainsString('institutional IdP password', $html);

            return true;
        });
        Mail::assertSentCount(1);
    }

    public function test_unknown_and_idp_only_accounts_receive_the_same_generic_response_without_mail(): void
    {
        $known = $this->post('/forgotPass', ['email' => 'account1@example.test']);
        $message = session('success');
        $this->post('/forgotPass', ['email' => 'unknown@example.test'])->assertRedirect('/forgot')->assertSessionHas('success', $message);
        $this->post('/forgotPass', ['email' => 'account4@example.test'])->assertRedirect('/forgot')->assertSessionHas('success', $message);
        Mail::assertSentCount(1);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'account4@example.test']);
        $known->assertSessionHas('success', $message);
    }

    public function test_old_email_only_reset_requests_cannot_change_a_password(): void
    {
        $before = DB::table('users')->where('id', 1)->value('password');
        $this->get('/reset?email=account1@example.test')->assertRedirect('/forgot');
        $this->postJson('/reset-password?email=account1@example.test', ['password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])->assertUnprocessable();
        $this->assertSame($before, DB::table('users')->where('id', 1)->value('password'));
    }

    public function test_valid_reset_form_keeps_token_out_of_action_and_has_private_headers(): void
    {
        $values = $this->token();
        $this->get('/reset?'.http_build_query($values))->assertOk()
            ->assertSee('name="token"', false)->assertSee($values['token'])
            ->assertSee('action="'.route('password.reset').'"', false)
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertDontSee('sienna-accessibility')->assertDontSee('chatbot-widget.js');
    }

    public function test_success_consumes_token_invalidates_sessions_and_preserves_idp_identity(): void
    {
        $values = $this->token();
        Cache::put('active_session_id:1', 'old-session', 120);
        DB::table('personal_access_tokens')->insert(['tokenable_type' => User::class, 'tokenable_id' => 1, 'name' => 'Old token', 'token' => str_repeat('a', 64)]);
        $this->post('/reset-password', $values + ['password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])
            ->assertRedirect(route('login.gateway'))->assertSessionHas('success')->assertSessionMissing('loginId');
        $user = User::find(1);
        $this->assertTrue(Hash::check('NewPassword!9', $user->password));
        $this->assertFalse(Hash::check('OriginalPassword!9', $user->password));
        $this->assertSame('idp-account-1', $user->idp_user_id);
        $this->assertTrue($user->has_local_password);
        $this->assertNotSame('old-remember-token', $user->remember_token);
        $this->assertNull(Cache::get('active_session_id:1'));
        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseHas('audit_logs', ['module' => 'Authentication', 'action' => 'password reset', 'affected_user_id' => 1]);
        $description = DB::table('audit_logs')->value('description');
        $this->assertStringNotContainsString($values['token'], $description);
        $this->assertStringNotContainsString('NewPassword!9', $description);
    }

    public function test_used_link_cannot_be_replayed(): void
    {
        $values = $this->token();
        $this->post('/reset-password', $values + ['password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])->assertRedirect(route('login.gateway'));
        $this->post('/reset-password', $values + ['password' => 'AttackerPassword!9', 'confirm_password' => 'AttackerPassword!9'])->assertRedirect('/forgot')->assertSessionHas('fail');
        $this->assertTrue(Hash::check('NewPassword!9', User::find(1)->password));
        $this->assertDatabaseCount('audit_logs', 1);
    }

    public function test_expired_token_is_rejected_on_get_and_post(): void
    {
        $values = $this->token();
        DB::table('password_reset_tokens')->update(['created_at' => now()->subMinutes(61)]);
        $this->get('/reset?'.http_build_query($values))->assertRedirect('/forgot');
        $this->post('/reset-password', $values + ['password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])->assertRedirect('/forgot')->assertSessionHas('fail');
        $this->assertTrue(Hash::check('OriginalPassword!9', User::find(1)->password));
    }

    public function test_token_is_bound_to_its_email_and_cannot_enable_idp_only_credentials(): void
    {
        $values = $this->token();
        $values['email'] = 'account2@example.test';
        $this->post('/reset-password', $values + ['password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])->assertRedirect('/forgot');
        $values = $this->token(4);
        $this->post('/reset-password', $values + ['password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])->assertRedirect('/forgot');
        $this->assertFalse(User::find(4)->has_local_password);
        $this->assertTrue(Hash::check('OriginalPassword!9', User::find(2)->password));
    }

    public function test_new_request_invalidates_previous_link_and_broker_throttles_repeated_mail(): void
    {
        $this->post('/forgotPass', ['email' => 'account1@example.test']);
        $first = Mail::sent(ForgotPassNotif::class)->first();
        $old = $this->linkValues($first->resetUrl);
        $this->post('/forgotPass', ['email' => 'account1@example.test']);
        Mail::assertSentCount(1);
        DB::table('password_reset_tokens')->update(['created_at' => now()->subSeconds(61)]);
        $this->post('/forgotPass', ['email' => 'account1@example.test']);
        Mail::assertSentCount(2);
        $this->get('/reset?'.http_build_query($old))->assertRedirect('/forgot');
        $new = $this->linkValues(Mail::sent(ForgotPassNotif::class)->last()->resetUrl);
        $this->get('/reset?'.http_build_query($new))->assertOk();
    }

    public function test_email_request_rate_limit_also_applies_to_unknown_accounts(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post('/forgotPass', ['email' => 'unknown@example.test'])->assertRedirect('/forgot');
        }
        $this->postJson('/forgotPass', ['email' => 'unknown@example.test'])->assertStatus(429);
        Mail::assertNothingSent();
    }

    public function test_password_validation_does_not_flash_passwords_or_token(): void
    {
        $values = $this->token();
        $this->from('/reset?'.http_build_query($values))->post('/reset-password', $values + ['password' => 'weak', 'confirm_password' => 'weak'])->assertSessionHasErrors('password');
        $this->assertArrayNotHasKey('password', session('_old_input', []));
        $this->assertArrayNotHasKey('confirm_password', session('_old_input', []));
        $this->assertArrayNotHasKey('token', session('_old_input', []));
        $this->assertTrue(Password::broker()->tokenExists(User::find(1), $values['token']));
    }

    public function test_previous_password_cannot_be_reused_and_does_not_consume_link(): void
    {
        $values = $this->token();
        $this->postJson('/reset-password', $values + ['password' => 'OriginalPassword!9', 'confirm_password' => 'OriginalPassword!9'])->assertUnprocessable();
        $this->assertTrue(Password::broker()->tokenExists(User::find(1), $values['token']));
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_reset_link_origin_comes_from_configuration_not_the_request_host(): void
    {
        $this->withServerVariables(['HTTP_HOST' => 'attacker.example.test'])->post('/forgotPass', ['email' => 'account1@example.test']);
        Mail::assertSent(ForgotPassNotif::class, fn ($mail) => str_starts_with($mail->resetUrl, 'https://internconnect.example.test/reset?') && ! str_contains($mail->resetUrl, 'attacker.example.test'));
    }

    public function test_incorrect_token_and_mismatched_confirmation_do_not_change_password(): void
    {
        $values = $this->token();
        $this->post('/reset-password', ['email' => $values['email'], 'token' => str_repeat('0', 64), 'password' => 'NewPassword!9', 'confirm_password' => 'NewPassword!9'])->assertRedirect('/forgot');
        $this->postJson('/reset-password', $values + ['password' => 'NewPassword!9', 'confirm_password' => 'DifferentPassword!9'])->assertUnprocessable();
        $this->assertTrue(Hash::check('OriginalPassword!9', User::find(1)->password));
        $this->assertTrue(Password::broker()->tokenExists(User::find(1), $values['token']));
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_mail_failure_does_not_disclose_account_existence_or_leave_a_new_token(): void
    {
        Mail::shouldReceive('to')->once()->with('account1@example.test')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Simulated transport failure'));
        $this->post('/forgotPass', ['email' => 'account1@example.test'])->assertRedirect('/forgot')->assertSessionHas('success');
        $this->assertStringContainsString('If this email belongs', session('success'));
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    private function token(int $id = 1): array
    {
        $user = User::find($id);

        return ['email' => $user->email, 'token' => Password::broker()->createToken($user)];
    }

    private function linkValues(string $url): array
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $values);

        return $values;
    }
}
