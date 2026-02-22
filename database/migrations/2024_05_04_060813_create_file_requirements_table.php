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
        Schema::create('file_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('fileName')->nullable()->default(NULL);
            $table->string('file')->nullable()->default(NULL);
            $table->string('status')->nullable()->default(NULL);
            $table->string('uploadedBy')->nullable()->default(NULL);
            $table->string('adviser')->nullable()->default(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_requirements');
    }
};
