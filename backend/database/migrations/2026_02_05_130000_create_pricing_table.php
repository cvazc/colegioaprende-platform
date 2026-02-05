<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->string('course_type', 100);
            $table->string('enrollment_type', 255);
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('MXN');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_type', 'enrollment_type'], 'pricing_course_enrollment_unique');
            $table->index('is_active', 'pricing_is_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
