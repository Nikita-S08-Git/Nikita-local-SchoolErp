<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Add unique constraint on (student_id, attendance_date) to prevent duplicate attendance records
 * 
 * This migration uses the CORRECT column name 'attendance_date' (not 'date')
 * to match the Attendance model's expected column name.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the attendance table exists
        if (!Schema::hasTable('attendance')) {
            echo "Attendance table does not exist. Skipping.\n";
            return;
        }

        // Check if student_id and attendance_date columns exist
        if (!Schema::hasColumn('attendance', 'student_id') ||
            !Schema::hasColumn('attendance', 'attendance_date')) {
            echo "Required columns (student_id or attendance_date) missing. Skipping.\n";
            return;
        }

        // Get existing duplicates (keeping the first record)
        $duplicates = DB::table('attendance')
            ->select('student_id', 'attendance_date', DB::raw('MIN(id) as min_id'))
            ->groupBy('student_id', 'attendance_date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            // Delete duplicate records, keeping only the first one
            foreach ($duplicates as $duplicate) {
                DB::table('attendance')
                    ->where('student_id', $duplicate->student_id)
                    ->where('attendance_date', $duplicate->attendance_date)
                    ->where('id', '>', $duplicate->min_id)
                    ->delete();
            }

            echo "Cleaned up " . $duplicates->count() . " duplicate attendance records.\n";
        }

        // Add unique constraint using CORRECT column name 'attendance_date'
        Schema::table('attendance', function (Blueprint $table) {
            // Try to drop any existing indexes on these columns
            try {
                DB::statement('DROP INDEX IF EXISTS attendance_date_student_id_index ON attendance');
            } catch (\Exception $e) {
                // Index may not exist, ignore
            }
            try {
                DB::statement('DROP INDEX IF EXISTS attendance_student_id_date_index ON attendance');
            } catch (\Exception $e) {
                // Index may not exist, ignore
            }
            try {
                DB::statement('DROP INDEX IF EXISTS attendance_student_date_unique ON attendance');
            } catch (\Exception $e) {
                // Index may not exist, ignore
            }

            // Add unique index on student_id and attendance_date (CORRECT column name)
            $table->unique(['student_id', 'attendance_date'], 'attendance_student_date_unique');
        });

        // Also add unique constraint on division_id + student_id + attendance_date
        if (Schema::hasColumn('attendance', 'division_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                try {
                    DB::statement('DROP INDEX IF EXISTS attendance_division_student_date_unique ON attendance');
                } catch (\Exception $e) {
                    // Ignore
                }

                $table->unique(['division_id', 'student_id', 'attendance_date'], 'attendance_division_student_date_unique');
            });
        }

        echo "Unique constraints added successfully on (student_id, attendance_date).\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            try {
                $table->dropIndex('attendance_student_date_unique');
                $table->dropIndex('attendance_division_student_date_unique');
            } catch (\Exception $e) {
                // Indexes may not exist, ignore
            }
        });
    }
};
