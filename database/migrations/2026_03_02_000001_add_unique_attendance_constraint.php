<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add unique constraint on (student_id, date) to prevent duplicate attendance records
     * First, clean up any existing duplicates
     */
    public function up(): void
    {
        // Check if the attendance table exists
        if (!Schema::hasTable('attendance')) {
            return;
        }

        // Check if student_id and date columns exist
        if (!Schema::hasColumn('attendance', 'student_id') || 
            !Schema::hasColumn('attendance', 'date')) {
            return;
        }

        // Get existing duplicates (keeping the first record)
        $duplicates = DB::table('attendance')
            ->select('student_id', 'date', DB::raw('MIN(id) as min_id'))
            ->groupBy('student_id', 'date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            // Delete duplicate records, keeping only the first one
            foreach ($duplicates as $duplicate) {
                DB::table('attendance')
                    ->where('student_id', $duplicate->student_id)
                    ->where('date', $duplicate->date)
                    ->where('id', '>', $duplicate->min_id)
                    ->delete();
            }
            
            echo "Cleaned up " . $duplicates->count() . " duplicate attendance records.\n";
        }

        // Add unique constraint
        Schema::table('attendance', function (Blueprint $table) {
            // Try to drop existing indexes if they exist (ignore errors)
            try {
                $table->dropIndex(['date', 'student_id']);
            } catch (\Exception $e) {
                // Index may not exist, ignore
            }
            try {
                $table->dropIndex(['student_id', 'date']);
            } catch (\Exception $e) {
                // Index may not exist, ignore
            }
            
            // Add unique index on student_id and date
            $table->unique(['student_id', 'date'], 'attendance_student_date_unique');
        });

        // Also add unique constraint on division_id + student_id + date
        if (Schema::hasColumn('attendance', 'division_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropUnique(['division_id', 'student_id', 'date']);
                $table->unique(['division_id', 'student_id', 'date'], 'attendance_division_student_date_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('attendance_student_date_unique');
            $table->dropUnique('attendance_division_student_date_unique');
            
            // Add back non-unique index
            $table->index(['date', 'student_id']);
        });
    }
};
