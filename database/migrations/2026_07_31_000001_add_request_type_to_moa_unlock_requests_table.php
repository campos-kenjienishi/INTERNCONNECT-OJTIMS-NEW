<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('moa_unlock_requests') && !Schema::hasColumn('moa_unlock_requests', 'request_type')) {
            Schema::table('moa_unlock_requests', function (Blueprint $table) {
                $table->string('request_type')->default('unlink')->after('company_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('moa_unlock_requests') && Schema::hasColumn('moa_unlock_requests', 'request_type')) {
            Schema::table('moa_unlock_requests', function (Blueprint $table) {
                $table->dropColumn('request_type');
            });
        }
    }
};
