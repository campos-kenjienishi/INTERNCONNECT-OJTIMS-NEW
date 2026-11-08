<?php

namespace Tests\Feature;

use App\Helpers\AuditLogger;
use App\Http\Controllers\MaintenanceController;
use App\Services\IdpService;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationAuditTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = config('database.default');
        config(['database.connections.auth_audit_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ], 'database.default' => 'auth_audit_testing']);
        DB::purge('auth_audit_testing');
        Http::preventStrayRequests();

        $maintenance = Mockery::mock(MaintenanceMode::class);
        $maintenance->shouldReceive('active')->andReturn(false);
        $this->app->instance(MaintenanceMode::class, $maintenance);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('idp_user_id')->nullable();
            $table->boolean('has_local_password')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->unsignedBigInteger('affected_user_id')->nullable();
            $table->string('affected_name')->nullable();
            $table->string('module');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
        DB::table('users')->insert([
            'id' => 1, 'full_name' => 'Audit Test User', 'email' => 'audit@example.test',
            'password' => Hash::make('TestOnlyPassword!9'), 'role' => '0',
            'idp_user_id' => 'idp-audit-user', 'has_local_password' => true,
        ]);
    }

    protected function tearDown(): void
    {
        DB::purge('auth_audit_testing');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public static function roles(): array
    {
        return [
            'student' => ['0', 'student', 'Student', '/student/home'],
            'coordinator' => ['1', 'faculty', 'OJT Coordinator', '/dashboard'],
            'professor' => ['2', 'faculty', 'Professor', '/professor/home'],
        ];
    }

    #[DataProvider('roles')]
    public function test_local_sign_in_logs_once_with_the_correct_actor_and_method(string $role, string $portal, string $label, string $destination): void
    {
        DB::table('users')->where('id', 1)->update(['role' => $role]);

        $this->post('/login-user', [
            'email' => 'audit@example.test', 'password' => 'TestOnlyPassword!9', 'portal' => $portal,
        ])->assertRedirect($destination)->assertSessionHas('loginId', 1)
            ->assertSessionHas('authentication_method', 'local');

        $this->assertLoginAudit($label, 'Local Sign-in');
    }

    #[DataProvider('roles')]
    public function test_idp_sign_in_logs_once_with_the_correct_actor_and_method(string $role, string $portal, string $label, string $destination): void
    {
        DB::table('users')->where('id', 1)->update(['role' => $role]);
        $idp = Mockery::mock(IdpService::class);
        $idp->shouldReceive('exchangeCode')->once()->with('test-code')->andReturn(['access_token' => 'test-access-token']);
        $idp->shouldReceive('validateToken')->once()->with('test-access-token')->andReturn((object) ['sub' => 'idp-audit-user']);
        $idp->shouldReceive('getUserInfo')->once()->with('test-access-token')->andReturn([
            'id' => 'idp-audit-user', 'email' => 'audit@example.test',
        ]);
        $this->app->instance(IdpService::class, $idp);
        config(['services.idp.enforce_state' => true]);

        $state = str_repeat('a', 64);
        $this->withSession(['idp_oauth_state' => $state, 'idp_oauth_started_at' => now()->timestamp, 'target_login_portal' => $portal])
            ->get('/auth/callback?code=test-code&state='.$state)
            ->assertRedirect($destination)->assertSessionHas('loginId', 1)
            ->assertSessionHas('authentication_method', 'idp');

        $this->assertLoginAudit($label, 'Identity Provider (IdP)');
    }

    public function test_invalid_password_does_not_record_a_successful_login(): void
    {
        $this->post('/login-user', [
            'email' => 'audit@example.test', 'password' => 'wrong-password', 'portal' => 'student',
        ])->assertSessionHas('fail')->assertSessionMissing('loginId');

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_wrong_portal_does_not_record_a_successful_login(): void
    {
        $this->post('/login-user', [
            'email' => 'audit@example.test', 'password' => 'TestOnlyPassword!9', 'portal' => 'faculty',
        ])->assertSessionHas('fail')->assertSessionMissing('loginId');

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public static function methods(): array
    {
        return [
            'local' => ['local', 'Local Sign-in'],
            'idp' => ['idp', 'Identity Provider (IdP)'],
            'legacy' => [null, 'Unknown (session started before method tracking)'],
        ];
    }

    #[DataProvider('methods')]
    public function test_logout_records_the_actor_before_clearing_the_session(?string $method, string $label): void
    {
        $this->withSession(['loginId' => 1, 'authentication_method' => $method]);
        $this->withCookie(config('session.cookie'), session()->getId());
        Cache::put('active_session_id:1', session()->getId(), 120);
        if ($method === 'idp') {
            session()->put('idp_access_token', 'test-access-token');
            $idp = Mockery::mock(IdpService::class);
            $idp->shouldReceive('logout')->once()->with('test-access-token');
            $this->app->instance(IdpService::class, $idp);
        }

        $this->post('/logout')->assertRedirect(route('login.gateway'))
            ->assertSessionMissing('loginId')->assertSessionMissing('authentication_method');

        $this->assertDatabaseCount('audit_logs', 1);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => 1, 'user_name' => 'Audit Test User', 'user_role' => 'Student',
            'module' => 'Authentication', 'action' => 'logout',
            'description' => 'User logged out; sign-in method: '.$label,
        ]);
        $this->assertNull(Cache::get('active_session_id:1'));
    }

    public function test_existing_module_logs_still_record_actor_and_affected_user(): void
    {
        $this->withSession(['loginId' => 1]);
        AuditLogger::log('Requirements', 'update', 'Approved a requirement', 1);

        $this->assertDatabaseHas('audit_logs', [
            'module' => 'Requirements', 'action' => 'update',
            'user_id' => 1, 'user_name' => 'Audit Test User', 'user_role' => 'Student',
            'affected_user_id' => 1, 'affected_name' => 'Audit Test User',
        ]);
    }

    public function test_audit_summary_counts_legacy_and_current_action_capitalization(): void
    {
        $this->withSession(['loginId' => 1]);
        foreach (['Create', 'create', 'Update', 'update', 'Delete', 'delete'] as $action) {
            AuditLogger::log('Audit test', $action, 'Test action', 1);
        }

        $view = app(MaintenanceController::class)->auditTrail();
        $counts = $view->getData()['actionCounts'];

        $this->assertSame(2, $counts->get('create'));
        $this->assertSame(2, $counts->get('update'));
        $this->assertSame(2, $counts->get('delete'));
        $this->assertSame(6, $view->getData()['logs']->count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'Create']);
    }

    private function assertLoginAudit(string $label, string $method): void
    {
        $this->assertDatabaseCount('audit_logs', 1);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => 1, 'user_name' => 'Audit Test User', 'user_role' => $label,
            'affected_user_id' => 1, 'module' => 'Authentication', 'action' => 'login',
            'description' => 'User logged in via '.$method.': audit@example.test',
        ]);
        $this->assertSame(session()->getId(), Cache::get('active_session_id:1'));
        $this->assertNotNull(DB::table('users')->where('id', 1)->value('last_login_at'));
        $description = DB::table('audit_logs')->value('description');
        $this->assertStringNotContainsString('test-access-token', $description);
        $this->assertStringNotContainsString('TestOnlyPassword!9', $description);
    }
}
