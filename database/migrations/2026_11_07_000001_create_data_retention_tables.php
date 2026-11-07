<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retention_states', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type', 20);
            $table->unsignedBigInteger('subject_id');
            $table->date('completed_on')->nullable();
            $table->date('clearance_finalized_on')->nullable();
            $table->date('hold_until')->nullable();
            $table->string('hold_reason', 500)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['subject_type', 'subject_id']);
        });

        Schema::create('retention_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30);
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('executed_by');
            $table->unsignedInteger('records_removed');
            $table->string('status', 30)->default('files_pending');
            $table->timestamps();
        });

        Schema::create('retention_file_deletions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained('retention_disposals')->cascadeOnDelete();
            $table->string('disk', 50);
            $table->string('path')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('last_error', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retention_file_deletions');
        Schema::dropIfExists('retention_disposals');
        Schema::dropIfExists('retention_states');
    }
};
