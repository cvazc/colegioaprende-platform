<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->foreignId('calendar_event_id')->constrained('calendar_events')->cascadeOnDelete();
            $table->date('reminder_date');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['student_id', 'calendar_event_id', 'reminder_date'], 'payment_reminder_unique');
            $table->foreign('student_id')->references('id')->on('student')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reminder_logs');
    }
};
