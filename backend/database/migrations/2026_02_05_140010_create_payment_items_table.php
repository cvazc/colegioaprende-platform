<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('name', 255);
            $table->string('course_type', 100);
            $table->string('enrollment_type', 255);
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('MXN');
            $table->unsignedInteger('credit_qty')->default(0);
            $table->boolean('auto_register')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['course_type', 'enrollment_type', 'code'],
                'payment_items_course_enrollment_code_unique'
            );
            $table->index(['course_type', 'enrollment_type'], 'payment_items_course_enrollment_idx');
            $table->index('is_active', 'payment_items_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
