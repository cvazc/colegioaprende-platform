<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('subject_id');
            $table->string('attempt_type', 20);
            $table->string('status', 20)->default('in_progress');
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('student_id', 'exam_attempts_student_idx');
            $table->index('subject_id', 'exam_attempts_subject_idx');
            $table->index('attempt_type', 'exam_attempts_type_idx');
            $table->index('status', 'exam_attempts_status_idx');
            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subject')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
