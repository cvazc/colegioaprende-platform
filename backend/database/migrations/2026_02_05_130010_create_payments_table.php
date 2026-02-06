<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('prospect_id');
            $table->string('provider', 50);
            $table->string('status', 30);
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('MXN');
            $table->string('provider_payment_id', 191)->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();

            $table->foreign('prospect_id')
                ->references('id')
                ->on('prospect')
                ->onDelete('cascade');

            $table->index('status', 'payments_status_idx');
            $table->index('provider', 'payments_provider_idx');
            $table->unique(['provider', 'provider_payment_id'], 'payments_provider_payment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
