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
        Schema::table('o_j_t_information', function (Blueprint $table) {
            $table->string('assigned_department')->nullable()->after('level');
            $table->string('student_role')->nullable()->after('assigned_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('o_j_t_information', function (Blueprint $table) {
            $table->dropColumn(['assigned_department', 'student_role']);
        });
    }
};
