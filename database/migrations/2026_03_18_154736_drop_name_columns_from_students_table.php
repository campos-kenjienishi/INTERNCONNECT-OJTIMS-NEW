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
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'first_name')) {
                $table->dropColumn('first_name');
            }

            if (Schema::hasColumn('students', 'middle_name')) {
                $table->dropColumn('middle_name');
            }

            if (Schema::hasColumn('students', 'last_name')) {
                $table->dropColumn('last_name');
            }

            if (Schema::hasColumn('students', 'suffix')) {
                $table->dropColumn('suffix');
            }

            if (Schema::hasColumn('students', 'full_name')) {
                $table->dropColumn('full_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'first_name')) {
                $table->string('first_name')->nullable()->default(null)->after('id');
            }

            if (!Schema::hasColumn('students', 'middle_name')) {
                $table->string('middle_name')->nullable()->default(null)->after('first_name');
            }

            if (!Schema::hasColumn('students', 'last_name')) {
                $table->string('last_name')->nullable()->default(null)->after('middle_name');
            }

            if (!Schema::hasColumn('students', 'suffix')) {
                $table->string('suffix')->nullable()->default(null)->after('last_name');
            }

            if (!Schema::hasColumn('students', 'full_name')) {
                $table->string('full_name')->nullable()->default(null)->after('suffix');
            }
        });
    }
};
