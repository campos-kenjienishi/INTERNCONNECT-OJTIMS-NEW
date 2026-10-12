<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_security_records', function (Blueprint $table) {
            $table->id();
            $table->string('storage_disk', 50);
            $table->string('storage_path')->unique();
            $table->string('filename');
            $table->string('directory', 100);
            $table->string('profile', 50);
            $table->string('extension', 20);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size_bytes');
            $table->char('sha256', 64)->index();
            $table->string('validation_status', 30)->default('validated');
            $table->string('scanner_driver', 50);
            $table->string('scan_status', 30);
            $table->timestamp('scanned_at')->nullable();
            $table->string('hosting_scanner', 50)->nullable();
            $table->string('hosting_scan_status', 30)->nullable();
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable()->index();
            $table->string('origin', 30);
            $table->string('lifecycle_status', 30)->default('active')->index();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('retired_at')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index(['scan_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_security_records');
    }
};
