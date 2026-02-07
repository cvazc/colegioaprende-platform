<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_template_id')->constrained('calendar_templates')->cascadeOnDelete();
            $table->string('event_code', 50)->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->boolean('is_payment')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['calendar_template_id', 'due_date']);
            $table->index(['is_payment', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
