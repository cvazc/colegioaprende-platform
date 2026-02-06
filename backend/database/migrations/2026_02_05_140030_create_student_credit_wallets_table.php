<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('balance')->default(0);
            $table->timestamps();

            $table->unique('student_id', 'student_credit_wallets_student_unique');
            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_credit_wallets');
    }
};
