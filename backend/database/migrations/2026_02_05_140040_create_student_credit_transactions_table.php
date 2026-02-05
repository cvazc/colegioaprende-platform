<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->integer('delta');
            $table->string('reason', 100);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('student_id', 'student_credit_transactions_student_idx');
            $table->index('payment_id', 'student_credit_transactions_payment_idx');
            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_credit_transactions');
    }
};
