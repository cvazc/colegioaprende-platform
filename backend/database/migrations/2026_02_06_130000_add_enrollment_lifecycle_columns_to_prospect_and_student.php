<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospect', function (Blueprint $table) {
            $table->string('status', 30)->default('pending_payment')->after('enrollment_type');
            $table->timestamp('last_payment_at')->nullable()->after('status');
            $table->timestamp('registered_as_student_at')->nullable()->after('last_payment_at');
            $table->index('status', 'prospect_status_idx');
        });

        Schema::table('student', function (Blueprint $table) {
            $table->string('status', 30)->default('active')->after('enrollment_type');
            $table->timestamp('profile_completed_at')->nullable()->after('status');
            $table->string('profile_photo_path', 255)->nullable()->after('profile_completed_at');
            $table->index('status', 'student_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('prospect', function (Blueprint $table) {
            $table->dropIndex('prospect_status_idx');
            $table->dropColumn(['status', 'last_payment_at', 'registered_as_student_at']);
        });

        Schema::table('student', function (Blueprint $table) {
            $table->dropIndex('student_status_idx');
            $table->dropColumn(['status', 'profile_completed_at', 'profile_photo_path']);
        });
    }
};
