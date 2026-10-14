<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('completion_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('professor_id')->nullable()->constrained('professors')->nullOnDelete();
            $table->string('file');
            $table->string('original_name')->nullable();
            $table->string('storage_directory')->default('completion-certificates');
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('pending_professor');
            $table->text('professor_remarks')->nullable();
            $table->foreignId('professor_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('professor_reviewed_at')->nullable();
            $table->text('coordinator_remarks')->nullable();
            $table->foreignId('coordinator_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('coordinator_reviewed_at')->nullable();
            $table->timestamp('superseded_at')->nullable();
            $table->unsignedBigInteger('legacy_file_requirement_id')->nullable()->unique();
            $table->timestamps();

            $table->index(['student_user_id', 'superseded_at']);
            $table->index(['professor_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        $this->copyLegacyCertificates();
    }

    public function down(): void
    {
        Schema::dropIfExists('completion_certificates');
    }

    private function copyLegacyCertificates(): void
    {
        if (! Schema::hasTable('file_requirements')) {
            return;
        }

        $legacyCertificates = DB::table('file_requirements')
            ->whereRaw("LOWER(TRIM(fileName)) IN ('certificate of completion', 'completion certificate')")
            ->orderBy('uploader_user_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $versions = [];
        $insertedIds = [];

        foreach ($legacyCertificates as $legacy) {
            $studentUserId = $legacy->uploader_user_id ?? null;

            if (! $studentUserId && ! empty($legacy->uploadedBy)) {
                $studentUserId = DB::table('users')
                    ->where('role', 0)
                    ->where('full_name', $legacy->uploadedBy)
                    ->value('id');
            }

            $student = $studentUserId
                ? DB::table('students')->where('user_id', $studentUserId)->first()
                : null;

            if (! $student || empty($legacy->file)) {
                continue;
            }

            $professorId = ! empty($student->adviser_name)
                ? DB::table('professors')->where('full_name', $student->adviser_name)->value('id')
                : null;

            $version = ($versions[$studentUserId] ?? 0) + 1;
            $versions[$studentUserId] = $version;
            $legacyStatus = (int) ($legacy->status ?? 0);
            $status = match ($legacyStatus) {
                1 => 'pending_coordinator',
                2 => 'professor_rejected',
                default => 'pending_professor',
            };

            $insertedId = DB::table('completion_certificates')->insertGetId([
                'student_id' => $student->id,
                'student_user_id' => $studentUserId,
                'professor_id' => $professorId,
                'file' => $legacy->file,
                'original_name' => 'Certificate of Completion.pdf',
                'storage_directory' => 'requirements',
                'version' => $version,
                'status' => $status,
                'professor_remarks' => $legacyStatus === 2 ? ($legacy->denial_reason ?? null) : null,
                'professor_reviewed_at' => in_array($legacyStatus, [1, 2], true) ? ($legacy->updated_at ?? now()) : null,
                'legacy_file_requirement_id' => $legacy->id,
                'created_at' => $legacy->created_at ?? now(),
                'updated_at' => $legacy->updated_at ?? now(),
            ]);

            if (isset($insertedIds[$studentUserId])) {
                DB::table('completion_certificates')
                    ->where('id', $insertedIds[$studentUserId])
                    ->update(['superseded_at' => $legacy->created_at ?? now()]);
            }

            $insertedIds[$studentUserId] = $insertedId;
        }
    }
};
