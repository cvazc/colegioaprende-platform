<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempt_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_option_id')->nullable();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedTinyInteger('points')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id'], 'exam_attempt_questions_unique');
            $table->index('attempt_id', 'exam_attempt_questions_attempt_idx');
            $table->index('question_id', 'exam_attempt_questions_question_idx');
            $table->foreign('attempt_id')
                ->references('id')
                ->on('exam_attempts')
                ->onDelete('cascade');
            $table->foreign('question_id')
                ->references('id')
                ->on('question_bank')
                ->onDelete('cascade');
            $table->foreign('answer_option_id')
                ->references('id')
                ->on('question_options')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_questions');
    }
};
