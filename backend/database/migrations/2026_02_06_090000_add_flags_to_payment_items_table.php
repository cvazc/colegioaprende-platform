<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->boolean('is_billable')->default(true)->after('is_active');
            $table->boolean('unlock_all_subjects')->default(false)->after('is_billable');
        });
    }

    public function down(): void
    {
        Schema::table('payment_items', function (Blueprint $table) {
            $table->dropColumn('is_billable');
            $table->dropColumn('unlock_all_subjects');
        });
    }
};
