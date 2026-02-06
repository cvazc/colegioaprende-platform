<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('label', 10)->nullable();
            $table->text('text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('question_id', 'question_options_question_idx');
            $table->foreign('question_id')
                ->references('id')
                ->on('question_bank')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
