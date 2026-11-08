<?php

namespace Tests\Feature;

use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Services\FacultyApiService;
use App\Services\IdpService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IdpCompatibilityTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config([
            'database.connections.idp_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'database.default' => 'idp_testing',
            'services.idp.enforce_state' => true,
            'services.idp.allow_missing_state' => true,
            'services.idp.login_attempt_seconds' => 600,
        ]);
        DB::purge('idp_testing');
        Cache::flush();
        Http::preventStrayRequests();
        Mail::fake();
        $this->withoutMiddleware(PreventRequestsDuringMaintenance::class);
        $this->mock(FacultyApiService::class);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('role');
            $table->string('idp_user_id')->nullable();
            $table->boolean('has_local_password')->default(false);
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name');
            $table->string('user_role');
            $table->unsignedBigInteger('affected_user_id')->nullable();
            $table->string('affected_name');
            $table->string('module');
            $table->string('action');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('idp_testing');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public static function roles(): array
    {
        return ['student' => ['0', '/student/home'], 'professor' => ['2', '/professor/home'], 'coordinator' => ['1', '/dashboard']];
    }

    #[DataProvider('roles')]
    public function test_code_only_callback_logs_in_existing_accounts_after_token_and_profile_validation(string $role, string $destination): void
    {
        DB::table('users')->insert(['id' => 1, 'full_name' => 'Synthetic Account', 'email' => 'idp@example.test',
            'role' => $role, 'idp_user_id' => 'synthetic-id']);
        $service = $this->mock(IdpService::class);
        $service->shouldReceive('exchangeCode')->once()->with('synthetic-code')->andReturn(['access_token' => 'synthetic-token']);
        $service->shouldReceive('validateToken')->once()->with('synthetic-token')->andReturn((object) ['sub' => 'synthetic-id']);
        $service->shouldReceive('getUserInfo')->once()->with('synthetic-token')
            ->andReturn(['id' => 'synthetic-id', 'email' => 'idp@example.test']);

        $this->withSession($this->pending())->get('/auth/callback?code=synthetic-code')
            ->assertRedirect($destination)->assertSessionHas('loginId', 1)
            ->assertSessionHas('authentication_method', 'idp')->assertSessionMissing('idp_oauth_state');
        $this->assertDatabaseHas('audit_logs', ['module' => 'Authentication', 'action' => 'login']);
        Mail::assertNothingSent();
    }

    public function test_direct_callback_is_rejected_before_external_requests(): void
    {
        $this->mock(IdpService::class)->shouldNotReceive('exchangeCode');
        $this->get('/auth/callback?code=synthetic-code')->assertSee('could not be verified')
            ->assertSessionMissing('loginId');
    }

    public function test_matching_state_still_reaches_token_validation_in_strict_mode(): void
    {
        config(['services.idp.allow_missing_state' => false]);
        $service = $this->mock(IdpService::class);
        $service->shouldReceive('exchangeCode')->once()->with('synthetic-code')->andReturn(['access_token' => 'synthetic-token']);
        $service->shouldReceive('validateToken')->once()->with('synthetic-token')->andReturn((object) ['sub' => 'synthetic-id']);
        $service->shouldReceive('getUserInfo')->once()->with('synthetic-token')->andReturn([]);

        $this->withSession($this->pending())
            ->get('/auth/callback?code=synthetic-code&state='.str_repeat('a', 64))
            ->assertSee('Incomplete user profile')->assertSessionMissing('loginId');
    }

    public function test_mismatched_state_is_rejected_even_in_compatibility_mode(): void
    {
        $this->mock(IdpService::class)->shouldNotReceive('exchangeCode');
        $this->withSession($this->pending())->get('/auth/callback?code=synthetic-code&state=wrong')
            ->assertSee('could not be verified')->assertSessionMissing('loginId')
            ->assertSessionMissing('idp_oauth_state');
    }

    public function test_strict_mode_rejects_missing_state(): void
    {
        config(['services.idp.allow_missing_state' => false]);
        $this->mock(IdpService::class)->shouldNotReceive('exchangeCode');
        $this->withSession($this->pending())->get('/auth/callback?code=synthetic-code')
            ->assertSee('could not be verified')->assertSessionMissing('loginId');
    }

    public function test_expired_attempt_is_rejected(): void
    {
        $this->mock(IdpService::class)->shouldNotReceive('exchangeCode');
        $this->withSession(array_merge($this->pending(), ['idp_oauth_started_at' => now()->subMinutes(10)->timestamp]))
            ->get('/auth/callback?code=synthetic-code')->assertSee('could not be verified')
            ->assertSessionMissing('loginId');
    }

    public function test_compatibility_does_not_skip_jwt_validation_and_consumes_failed_attempts(): void
    {
        $service = $this->mock(IdpService::class);
        $service->shouldReceive('exchangeCode')->once()->andReturn(['access_token' => 'invalid-token']);
        $service->shouldReceive('validateToken')->once()->with('invalid-token')
            ->andThrow(new \RuntimeException('Synthetic token rejection'));
        $service->shouldNotReceive('getUserInfo');

        $this->withSession($this->pending())->get('/auth/callback?code=synthetic-code')
            ->assertSessionMissing('loginId')->assertSessionMissing('idp_oauth_state');
        $this->get('/auth/callback?code=synthetic-code')->assertSee('could not be verified')
            ->assertSessionMissing('loginId');
    }

    public function test_redirect_starts_a_fresh_attempt_and_still_sends_state(): void
    {
        $this->mock(IdpService::class)->shouldReceive('getAuthorizeUrl')->once()
            ->withArgs(fn ($state) => is_string($state) && strlen($state) === 64)
            ->andReturn('https://identity.example.test/authorize');

        $this->get('/auth/redirect-to-idp?portal=student')->assertRedirect('https://identity.example.test/authorize')
            ->assertSessionHas('idp_oauth_state')->assertSessionHas('idp_oauth_started_at');
    }

    private function pending(): array
    {
        return ['idp_oauth_state' => str_repeat('a', 64), 'idp_oauth_started_at' => now()->timestamp];
    }
}
