<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ojt_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('ojt_evaluations', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ojt_evaluations', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('comments');
        });
    }
};
