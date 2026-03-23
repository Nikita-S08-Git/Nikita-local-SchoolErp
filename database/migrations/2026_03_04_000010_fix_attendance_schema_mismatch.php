<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration to standardize attendance table schema
 * 
 * Fixes:
 * - Rename attendance_date to date (to match model)
 * - Standardize status enum to lowercase
 * - Add missing columns if needed
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename attendance_date to date if it exists
        if (Schema::hasColumn('attendance', 'attendance_date') && !Schema::hasColumn('attendance', 'date')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->renameColumn('attendance_date', 'date');
            });
        }

        // 2. Standardize status enum to lowercase
        // MySQL requires dropping and re-adding enum columns
        if (Schema::hasColumn('attendance', 'status')) {
            // Check current enum values
            $columnType = DB::select("SHOW COLUMNS FROM attendance WHERE Field = 'status'");
            
            if (!empty($columnType) && isset($columnType[0]->Type)) {
                $currentType = $columnType[0]->Type;
                
                // Check if enum has capitalized values
                if (str_contains($currentType, 'Present') || 
                    str_contains($currentType, 'Absent') || 
                    str_contains($currentType, 'Late')) {
                    
                    // First, update any capitalized values to lowercase
                    DB::table('attendance')->where('status', 'Present')->update(['status' => 'present']);
                    DB::table('attendance')->where('status', 'Absent')->update(['status' => 'absent']);
                    DB::table('attendance')->where('status', 'Late')->update(['status' => 'late']);
                    
                    // Also handle 'Excused' if present
                    DB::table('attendance')->where('status', 'Excused')->update(['status' => 'excused']);
                    
                    // Modify the enum column using raw SQL
                    DB::statement("ALTER TABLE attendance MODIFY COLUMN status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'present'");
                }
            }
        }

        // 3. Add timetable_id column if it doesn't exist (needed for model)
        if (!Schema::hasColumn('attendance', 'timetable_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->foreignId('timetable_id')->nullable()->constrained('timetables')->onDelete('set null')->after('academic_session_id');
            });
        }

        // 4. Add ip_address column if it doesn't exist (needed for model)
        if (!Schema::hasColumn('attendance', 'ip_address')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->after('remarks');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the status enum change
        if (Schema::hasColumn('attendance', 'status')) {
            DB::statement("ALTER TABLE attendance MODIFY COLUMN status ENUM('Present', 'Absent', 'Late') DEFAULT 'Present'");
        }

        // Reverse the column rename
        if (Schema::hasColumn('attendance', 'date') && !Schema::hasColumn('attendance', 'attendance_date')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->renameColumn('date', 'attendance_date');
            });
        }
    }
};
