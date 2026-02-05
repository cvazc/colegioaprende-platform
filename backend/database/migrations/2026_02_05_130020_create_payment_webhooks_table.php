<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('event_type', 100)->nullable();
            $table->string('provider_event_id', 191)->nullable();
            $table->json('payload');
            $table->timestamp('received_at')->useCurrent();

            $table->index('provider', 'payment_webhooks_provider_idx');
            $table->index('event_type', 'payment_webhooks_event_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhooks');
    }
};
