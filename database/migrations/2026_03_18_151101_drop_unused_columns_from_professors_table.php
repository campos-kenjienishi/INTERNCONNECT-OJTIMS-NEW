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
        Schema::table('professors', function (Blueprint $table) {
            if (Schema::hasColumn('professors', 'year_and_section')) {
                $table->dropColumn('year_and_section');
            }

            if (Schema::hasColumn('professors', 'course')) {
                $table->dropColumn('course');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professors', function (Blueprint $table) {
            if (!Schema::hasColumn('professors', 'year_and_section')) {
                $table->string('year_and_section')->nullable()->default(null)->after('full_name');
            }

            if (!Schema::hasColumn('professors', 'course')) {
                $table->string('course')->nullable()->default(null)->after('year_and_section');
            }
        });
    }
};
