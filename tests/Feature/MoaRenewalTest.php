<?php

namespace Tests\Feature;

use App\Http\Controllers\CompanyController;
use App\Models\Company;
use App\Models\MoaRenewal;
use App\Services\MoaValidityService;
use App\Services\PrivateDocumentStorage;
use App\Services\SecureUploadService;
use App\Services\UploadSecurityRecorder;
use App\Services\Uploads\SecureUploadResult;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Mockery;
use Tests\TestCase;

class MoaRenewalTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = (string) config('database.default');
        config()->set('database.connections.moa_renewal_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        config()->set('database.default', 'moa_renewal_testing');
        DB::purge('moa_renewal_testing');

        $this->createTables();
    }

    protected function tearDown(): void
    {
        DB::purge('moa_renewal_testing');
        config()->set('database.default', $this->originalConnection);

        parent::tearDown();
    }

    public function test_coordinator_renewal_preserves_the_previous_file_and_records_history(): void
    {
        DB::table('users')->insert([
            'id' => 7,
            'full_name' => 'OJT Coordinator',
            'role' => 1,
        ]);
        DB::table('companies')->insert([
            'id' => 12,
            'company_name' => 'Partner Company',
            'file' => 'original-moa.pdf',
            'date_notarized' => '2023-10-01',
            'valid_until' => '2026-10-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('file_requirements')->insert([
            'id' => 30,
            'fileName' => 'Notarized MOA',
            'file' => 'original-moa.pdf',
        ]);

        Session::put('loginId', 7);

        $secureUploads = Mockery::mock(SecureUploadService::class);
        $secureUploads->shouldReceive('store')->once()->andReturn(new SecureUploadResult(
            'documents/moa/renewed-moa.pdf',
            'renewed-moa.pdf',
            'pdf',
            'application/pdf',
            1024,
            hash('sha256', 'renewed'),
            'clean',
            91
        ));

        $documents = Mockery::mock(PrivateDocumentStorage::class);
        $documents->shouldNotReceive('delete');

        $securityEvidence = Mockery::mock(UploadSecurityRecorder::class);
        $securityEvidence->shouldReceive('link')
            ->once()
            ->withArgs(fn ($recordId, $source, $userId) => $recordId === 91
                && $source instanceof MoaRenewal
                && $userId === 7);

        $controller = new CompanyController(
            $secureUploads,
            $documents,
            $securityEvidence,
            new MoaValidityService()
        );

        $request = Request::create(
            '/company/12/renew',
            'POST',
            [
                'renewal_date_notarized' => '2026-10-12',
                'renewal_valid_until' => '2029-10-12',
                'notes' => 'Renewed for another agreement period.',
            ],
            [],
            ['file' => UploadedFile::fake()->create('renewed.pdf', 100, 'application/pdf')]
        );
        $request->headers->set('referer', 'http://localhost/MOA');
        app()->instance('request', $request);

        $response = $controller->renewMoa($request, 12);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('moa_renewals', [
            'company_id' => 12,
            'previous_file' => 'original-moa.pdf',
            'new_file' => 'renewed-moa.pdf',
            'renewed_by' => 7,
        ]);
        $this->assertDatabaseHas('companies', [
            'id' => 12,
            'file' => 'renewed-moa.pdf',
            'valid_until' => '2029-10-12',
        ]);
        $this->assertDatabaseHas('file_requirements', [
            'id' => 30,
            'file' => 'renewed-moa.pdf',
        ]);
        $this->assertSame('original-moa.pdf', MoaRenewal::firstOrFail()->previous_file);
        $this->assertSame('renewed-moa.pdf', Company::findOrFail(12)->file);
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->unsignedTinyInteger('role');
        });
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('file');
            $table->date('date_notarized')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });
        Schema::create('file_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('fileName');
            $table->string('file');
            $table->timestamps();
        });
        Schema::create('moa_renewals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->date('previous_date_notarized')->nullable();
            $table->date('previous_valid_until')->nullable();
            $table->string('previous_file');
            $table->date('new_date_notarized');
            $table->date('new_valid_until');
            $table->string('new_file');
            $table->unsignedBigInteger('renewed_by')->nullable();
            $table->timestamp('renewed_at');
            $table->text('notes')->nullable();
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
