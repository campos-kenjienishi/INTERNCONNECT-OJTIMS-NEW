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
        Schema::create('professor_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();
    
            // Define foreign keys
            $table->foreign('professor_id')->references('id')->on('professors')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_subject');
    }
};
