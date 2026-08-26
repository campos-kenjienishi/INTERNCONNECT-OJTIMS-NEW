<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'is_inhouse_ojt')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('is_inhouse_ojt')->default(false)->after('course');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'is_inhouse_ojt')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('is_inhouse_ojt');
            });
        }
    }
};
