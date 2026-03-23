<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'division_id')) {
                $table->foreignId('division_id')->after('student_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('attendance', 'subject_id')) {
                $table->foreignId('subject_id')->after('division_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Add indexes for better query performance
            $table->index(['division_id', 'attendance_date']);
            $table->index(['subject_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['subject_id']);
            $table->dropIndex(['division_id', 'attendance_date']);
            $table->dropIndex(['subject_id', 'attendance_date']);
            $table->dropColumn(['division_id', 'subject_id']);
        });
    }
};
