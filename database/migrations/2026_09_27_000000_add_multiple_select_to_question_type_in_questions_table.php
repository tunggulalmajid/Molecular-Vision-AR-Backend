<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `questions` MODIFY COLUMN `question_type` ENUM('multiple_choice', 'multiple_select', 'true_false', 'short_answer', 'matching') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `questions` MODIFY COLUMN `question_type` ENUM('multiple_choice', 'true_false', 'short_answer', 'matching') NOT NULL");
        }
    }
};
