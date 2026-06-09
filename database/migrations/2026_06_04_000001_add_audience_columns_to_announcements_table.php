<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'audience')) {
                $table->string('audience')->default('all_students')->after('content');
            }

            if (!Schema::hasColumn('announcements', 'target_course')) {
                $table->string('target_course')->nullable()->after('audience');
            }

            if (!Schema::hasColumn('announcements', 'target_room')) {
                $table->string('target_room')->nullable()->after('target_course');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'target_room')) {
                $table->dropColumn('target_room');
            }

            if (Schema::hasColumn('announcements', 'target_course')) {
                $table->dropColumn('target_course');
            }

            if (Schema::hasColumn('announcements', 'audience')) {
                $table->dropColumn('audience');
            }
        });
    }
};