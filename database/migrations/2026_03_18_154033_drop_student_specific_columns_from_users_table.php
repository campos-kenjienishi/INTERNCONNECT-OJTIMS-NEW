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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'studentNum')) {
                $table->dropColumn('studentNum');
            }

            if (Schema::hasColumn('users', 'year_and_section')) {
                $table->dropColumn('year_and_section');
            }

            if (Schema::hasColumn('users', 'course')) {
                $table->dropColumn('course');
            }

            if (Schema::hasColumn('users', 'adviser_name')) {
                $table->dropColumn('adviser_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'studentNum')) {
                $table->string('studentNum')->nullable()->default(null)->after('email');
            }

            if (!Schema::hasColumn('users', 'year_and_section')) {
                $table->string('year_and_section')->nullable()->default(null)->after('address');
            }

            if (!Schema::hasColumn('users', 'course')) {
                $table->string('course')->nullable()->default(null)->after('year_and_section');
            }

            if (!Schema::hasColumn('users', 'adviser_name')) {
                $table->string('adviser_name')->nullable()->default(null)->after('course');
            }
        });
    }
};
