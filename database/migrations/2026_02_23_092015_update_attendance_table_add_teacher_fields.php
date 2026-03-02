<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate attendance for same student & date
            $table->unique(['student_id', 'date', 'division_id'], 'att_student_date_div_unique');

            // Add index for faster queries (only on existing columns)
            $table->index(['date'], 'att_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('att_student_date_div_unique');

            // Drop index
            $table->dropIndex('att_date_idx');
        });
    }
};
