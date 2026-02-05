<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('student_id')->nullable()->after('prospect_id');
            $table->string('item_code', 50)->nullable()->after('status');
            $table->string('provider_reference_id', 191)->nullable()->after('provider_payment_id');
            $table->timestamp('approved_at')->nullable()->after('provider_payload');

            $table->index('student_id', 'payments_student_id_idx');
            $table->index('item_code', 'payments_item_code_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_student_id_idx');
            $table->dropIndex('payments_item_code_idx');
            $table->dropColumn('student_id');
            $table->dropColumn('item_code');
            $table->dropColumn('provider_reference_id');
            $table->dropColumn('approved_at');
        });
    }
};
