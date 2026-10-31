<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->json('targeting')->nullable();
            $table->string('publication_key', 64)->nullable()->unique();
        });

        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['announcement_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('ojt_professor_designations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('designated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('course');
            $table->string('school_year', 9);
            $table->string('semester', 30)->default('');
            $table->timestamps();
            $table->unique(['user_id', 'course', 'school_year', 'semester'], 'ojt_professor_designation_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
        Schema::dropIfExists('ojt_professor_designations');
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique(['publication_key']);
            $table->dropColumn(['targeting', 'publication_key']);
        });
    }
};
