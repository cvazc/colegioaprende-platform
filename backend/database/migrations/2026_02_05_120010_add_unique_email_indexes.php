<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->unique('email', 'student_email_unique');
        });

        Schema::table('prospect', function (Blueprint $table) {
            $table->unique('email', 'prospect_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropUnique('student_email_unique');
        });

        Schema::table('prospect', function (Blueprint $table) {
            $table->dropUnique('prospect_email_unique');
        });
    }
};
