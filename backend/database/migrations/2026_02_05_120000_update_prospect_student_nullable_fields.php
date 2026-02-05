<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE prospect MODIFY movil_phone VARCHAR(15) NULL');
        DB::statement('ALTER TABLE prospect MODIFY birth_date DATE NULL');
        DB::statement('ALTER TABLE prospect MODIFY actual_address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE prospect MODIFY city VARCHAR(255) NULL');

        DB::statement('ALTER TABLE student MODIFY movil_phone VARCHAR(15) NULL');
        DB::statement('ALTER TABLE student MODIFY birth_date DATE NULL');
        DB::statement('ALTER TABLE student MODIFY actual_address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE student MODIFY city VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE prospect MODIFY movil_phone VARCHAR(15) NOT NULL');
        DB::statement('ALTER TABLE prospect MODIFY birth_date DATE NOT NULL');
        DB::statement('ALTER TABLE prospect MODIFY actual_address VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE prospect MODIFY city VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE student MODIFY movil_phone VARCHAR(15) NOT NULL');
        DB::statement('ALTER TABLE student MODIFY birth_date DATE NOT NULL');
        DB::statement('ALTER TABLE student MODIFY actual_address VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE student MODIFY city VARCHAR(255) NOT NULL');
    }
};
