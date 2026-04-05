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
        Schema::table('ojt_evaluation_templates', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('description');
            $table->unsignedBigInteger('previous_template_id')->nullable()->after('version');

            $table->foreign('previous_template_id')
                ->references('id')
                ->on('ojt_evaluation_templates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ojt_evaluation_templates', function (Blueprint $table) {
            $table->dropForeign(['previous_template_id']);
            $table->dropColumn(['version', 'previous_template_id']);
        });
    }
};
