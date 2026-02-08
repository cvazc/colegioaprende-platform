<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->integer('student_id');
            $table->integer('assigned_by_employee_id')->nullable();
            $table->timestamp('assigned_at');
            $table->string('status', 20)->default('active');
            $table->string('source', 20)->default('auto');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('student_id', 'cohort_members_student_unique');
            $table->unique(['cohort_id', 'student_id'], 'cohort_members_cohort_student_unique');
            $table->index(['cohort_id', 'status'], 'cohort_members_cohort_status_idx');
            $table->foreign('student_id')->references('id')->on('student')->cascadeOnDelete();
            $table->foreign('assigned_by_employee_id')->references('id')->on('employee')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_members');
    }
};
