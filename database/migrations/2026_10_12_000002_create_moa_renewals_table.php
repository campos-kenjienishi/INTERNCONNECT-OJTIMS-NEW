<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moa_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('previous_date_notarized')->nullable();
            $table->date('previous_valid_until')->nullable();
            $table->string('previous_file');
            $table->date('new_date_notarized');
            $table->date('new_valid_until');
            $table->string('new_file');
            $table->foreignId('renewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('renewed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'renewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moa_renewals');
    }
};
