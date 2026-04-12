<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ojt_evaluation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('ojt_evaluation_template_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('section')->nullable();
            $table->string('label');
            $table->string('input_type')->default('rating'); // rating|text
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->foreign('template_id')
                ->references('id')
                ->on('ojt_evaluation_templates')
                ->onDelete('cascade');
        });

        Schema::create('ojt_evaluation_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('template_id');
            $table->string('student_num');
            $table->string('student_name')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_email');
            $table->string('token')->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('sent'); // sent|opened|submitted|expired
            $table->timestamps();

            $table->index(['student_id', 'class_id']);
            $table->index(['supervisor_email']);

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('ojt_evaluation_templates')->onDelete('cascade');
        });

        Schema::create('ojt_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id')->unique();
            $table->unsignedBigInteger('template_id');
            $table->string('supervisor_name')->nullable();
            $table->longText('responses_json')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('ojt_evaluation_requests')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('ojt_evaluation_templates')->onDelete('cascade');
        });

        $templateId = DB::table('ojt_evaluation_templates')->insertGetId([
            'title' => 'OJT Supervisor Evaluation Form',
            'description' => 'Default evaluation template based on the current manual form.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $items = [
            ['section' => 'Work Habits', 'label' => 'Attendance and punctuality', 'order' => 1],
            ['section' => 'Work Habits', 'label' => 'Dependability and responsibility', 'order' => 2],
            ['section' => 'Work Habits', 'label' => 'Initiative and willingness to learn', 'order' => 3],
            ['section' => 'Work Skills', 'label' => 'Quality of work output', 'order' => 4],
            ['section' => 'Work Skills', 'label' => 'Knowledge and application of skills', 'order' => 5],
            ['section' => 'Work Skills', 'label' => 'Productivity and efficiency', 'order' => 6],
            ['section' => 'Social Skills', 'label' => 'Communication with co-workers/superiors', 'order' => 7],
            ['section' => 'Social Skills', 'label' => 'Teamwork and cooperation', 'order' => 8],
            ['section' => 'Social Skills', 'label' => 'Professional attitude and behavior', 'order' => 9],
            ['section' => null, 'label' => 'Comments and suggestions', 'order' => 10, 'input_type' => 'text', 'is_required' => 0],
        ];

        foreach ($items as $item) {
            DB::table('ojt_evaluation_template_items')->insert([
                'template_id' => $templateId,
                'section' => $item['section'] ?? null,
                'label' => $item['label'],
                'input_type' => $item['input_type'] ?? 'rating',
                'display_order' => $item['order'],
                'is_required' => $item['is_required'] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ojt_evaluations');
        Schema::dropIfExists('ojt_evaluation_requests');
        Schema::dropIfExists('ojt_evaluation_template_items');
        Schema::dropIfExists('ojt_evaluation_templates');
    }
};
