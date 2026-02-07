<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_calendars', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->foreignId('calendar_template_id')->constrained('calendar_templates')->cascadeOnDelete();
            $table->integer('assigned_by_employee_id')->nullable();
            $table->date('assigned_start_date')->nullable();
            $table->timestamps();

            $table->unique('student_id');
            $table->foreign('student_id')->references('id')->on('student')->cascadeOnDelete();
            $table->foreign('assigned_by_employee_id')
                ->references('id')
                ->on('employee')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_calendars');
    }
};
