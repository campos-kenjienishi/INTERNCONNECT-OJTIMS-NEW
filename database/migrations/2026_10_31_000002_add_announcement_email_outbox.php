<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->json('email_payload')->nullable();
        });

        Schema::create('announcement_email_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('email')->nullable();
            $table->string('recipient_role', 1);
            $table->string('status', 20)->default('queued')->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('last_error', 100)->nullable();
            $table->timestamps();
            $table->unique(['announcement_id', 'user_id'], 'announcement_email_recipient_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_email_deliveries');
        Schema::table('announcements', fn (Blueprint $table) => $table->dropColumn('email_payload'));
    }
};
