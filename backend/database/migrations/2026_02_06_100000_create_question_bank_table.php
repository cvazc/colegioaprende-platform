<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id');
            $table->string('type', 20);
            $table->text('prompt');
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('difficulty')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('subject_id', 'question_bank_subject_idx');
            $table->index('type', 'question_bank_type_idx');
            $table->index('is_active', 'question_bank_active_idx');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subject')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};
