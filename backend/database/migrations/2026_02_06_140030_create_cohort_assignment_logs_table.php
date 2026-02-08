<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->foreignId('from_cohort_id')->nullable()->constrained('cohorts')->nullOnDelete();
            $table->foreignId('to_cohort_id')->nullable()->constrained('cohorts')->nullOnDelete();
            $table->string('action', 30);
            $table->string('reason', 255)->nullable();
            $table->integer('actor_employee_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'action'], 'cohort_logs_student_action_idx');
            $table->foreign('student_id')->references('id')->on('student')->cascadeOnDelete();
            $table->foreign('actor_employee_id')->references('id')->on('employee')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_assignment_logs');
    }
};
