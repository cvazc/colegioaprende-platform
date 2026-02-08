<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->string('field', 50);
            $table->string('operator', 20)->default('equals');
            $table->string('value', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['cohort_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_rules');
    }
};
