<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_exam_configs', function (Blueprint $table) {
            $table->unsignedTinyInteger('difficulty_min')->default(1)->after('final_questions');
            $table->unsignedTinyInteger('difficulty_max')->default(5)->after('difficulty_min');
        });
    }

    public function down(): void
    {
        Schema::table('subject_exam_configs', function (Blueprint $table) {
            $table->dropColumn('difficulty_min');
            $table->dropColumn('difficulty_max');
        });
    }
};
