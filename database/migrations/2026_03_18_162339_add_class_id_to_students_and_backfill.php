<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('students', 'user_id') && Schema::hasColumn('users', 'class_id')) {
            DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->whereNull('students.class_id')
                ->whereNotNull('users.class_id')
                ->update(['students.class_id' => DB::raw('users.class_id')]);
        }

        if (Schema::hasColumn('students', 'school_year_start') && Schema::hasColumn('students', 'school_year_end')) {
            DB::table('students')
                ->where('school_year_start', 2023)
                ->where('school_year_end', 2024)
                ->update(['class_id' => 24]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'class_id')) {
                $table->dropColumn('class_id');
            }
        });
    }
};
