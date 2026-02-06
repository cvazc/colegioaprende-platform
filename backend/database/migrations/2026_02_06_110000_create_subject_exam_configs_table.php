<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_exam_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id');
            $table->unsignedInteger('practice_questions')->default(10);
            $table->unsignedInteger('midterm_questions')->default(20);
            $table->unsignedInteger('final_questions')->default(40);
            $table->boolean('allow_open_practice')->default(true);
            $table->timestamps();

            $table->unique('subject_id', 'subject_exam_configs_subject_unique');
            $table->index('subject_id', 'subject_exam_configs_subject_idx');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subject')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_exam_configs');
    }
};
