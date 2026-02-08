<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('course_type', 100)->nullable();
            $table->string('enrollment_type', 255)->nullable();
            $table->foreignId('calendar_template_id')->nullable()->constrained('calendar_templates')->nullOnDelete();
            $table->unsignedInteger('capacity')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('auto_assign')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by_employee_id')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index(['course_type', 'enrollment_type'], 'cohorts_course_enrollment_idx');
            $table->foreign('created_by_employee_id')->references('id')->on('employee')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
