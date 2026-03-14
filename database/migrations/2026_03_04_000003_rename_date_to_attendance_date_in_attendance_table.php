<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to rename 'date' column to 'attendance_date' in attendance table
 * and add missing columns for unified attendance model
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if 'date' column exists and rename it to 'attendance_date'
        if (Schema::hasColumn('attendance', 'date') && !Schema::hasColumn('attendance', 'attendance_date')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->renameColumn('date', 'attendance_date');
            });
        }
        
        // Add missing columns if they don't exist
        if (!Schema::hasColumn('attendance', 'check_in_time')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->time('check_in_time')->nullable()->after('attendance_date');
            });
        }
        
        if (!Schema::hasColumn('attendance', 'check_out_time')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->time('check_out_time')->nullable()->after('check_in_time');
            });
        }
        
        if (!Schema::hasColumn('attendance', 'division_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->unsignedBigInteger('division_id')->nullable()->after('student_id');
                $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            });
        }
        
        if (!Schema::hasColumn('attendance', 'academic_session_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->unsignedBigInteger('academic_session_id')->nullable()->after('division_id');
                $table->foreign('academic_session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Reverse column rename
            if (Schema::hasColumn('attendance', 'attendance_date') && !Schema::hasColumn('attendance', 'date')) {
                $table->renameColumn('attendance_date', 'date');
            }
            
            // Drop added columns
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'check_in_time', 'check_out_time', 'academic_session_id']);
        });
    }
};
