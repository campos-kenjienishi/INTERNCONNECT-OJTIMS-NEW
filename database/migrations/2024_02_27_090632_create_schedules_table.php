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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code')->nullable()->default(NULL);
            $table->string('course')->nullable()->default(NULL);
            $table->string('academic_year')->nullable()->default(NULL);
            $table->string('semester')->nullable()->default(NULL);
            $table->string('schedule_day')->nullable()->default(NULL);
            $table->string('schedule_time')->nullable()->default(NULL);
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
