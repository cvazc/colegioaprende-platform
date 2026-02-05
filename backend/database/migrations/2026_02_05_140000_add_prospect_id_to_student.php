<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->unsignedBigInteger('prospect_id')->nullable()->after('id');
            $table->unique('prospect_id', 'student_prospect_id_unique');
            $table->index('prospect_id', 'student_prospect_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropUnique('student_prospect_id_unique');
            $table->dropIndex('student_prospect_id_idx');
            $table->dropColumn('prospect_id');
        });
    }
};
