<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Consolidation Migration - Fix All Pending Migration Issues
 * 
 * This migration fixes:
 * 1. Ensures attendance table uses 'attendance_date' column (not 'date')
 * 2. Standardizes status enum to lowercase (present, absent, late, excused)
 * 3. Adds missing timetable_id and ip_address columns to attendance
 * 4. Adds proper indexes for performance
 * 5. Ensures timetables table has proper status enum values
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ============================================
        // FIX 1: Attendance Table Column Standardization
        // ============================================
        
        // Ensure attendance table exists from original migration
        if (!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('cascade');
                $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('cascade');
                $table->date('attendance_date');
                $table->enum('status', ['present', 'absent', 'late'])->default('present');
                $table->time('check_in_time')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->unique(['student_id', 'attendance_date']);
                $table->index(['attendance_date', 'division_id']);
            });
        }

        // Fix attendance_date column if it was renamed to 'date'
        if (Schema::hasColumn('attendance', 'date') && !Schema::hasColumn('attendance', 'attendance_date')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->renameColumn('date', 'attendance_date');
            });
        }

        // ============================================
        // FIX 2: Add Missing Columns to Attendance
        // ============================================
        
        // Add timetable_id if not exists
        if (!Schema::hasColumn('attendance', 'timetable_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->foreignId('timetable_id')->nullable()->after('student_id')
                      ->constrained('timetables')->onDelete('cascade');
            });
        }

        // Add ip_address if not exists
        if (!Schema::hasColumn('attendance', 'ip_address')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->after('remarks');
            });
        }

        // Add check_out_time if not exists
        if (!Schema::hasColumn('attendance', 'check_out_time')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->time('check_out_time')->nullable()->after('check_in_time');
            });
        }

        // ============================================
        // FIX 3: Standardize Status Enum to Lowercase
        // ============================================
        
        try {
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
                    DB::table('attendance')->where('status', 'Excused')->update(['status' => 'excused']);
                    
                    // Modify the enum column using raw SQL
                    DB::statement("ALTER TABLE attendance MODIFY COLUMN status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'present'");
                }
            }
        } catch (\Exception $e) {
            // Enum might already be correct, continue
            \Log::info('Attendance status enum already standardized or error: ' . $e->getMessage());
        }

        // ============================================
        // FIX 4: Add Performance Indexes
        // ============================================
        
        try {
            $indexes = DB::select("SHOW INDEX FROM attendance");
            $indexNames = array_column($indexes, 'Key_name');
            
            // Add index on student_id and attendance_date if not exists
            if (!in_array('attendance_student_date_idx', $indexNames)) {
                Schema::table('attendance', function (Blueprint $table) {
                    $table->index(['student_id', 'attendance_date'], 'attendance_student_date_idx');
                });
            }
            
            // Add index on timetable_id and attendance_date if not exists
            if (Schema::hasColumn('attendance', 'timetable_id')) {
                if (!in_array('attendance_timetable_date_idx', $indexNames)) {
                    Schema::table('attendance', function (Blueprint $table) {
                        $table->index(['timetable_id', 'attendance_date'], 'attendance_timetable_date_idx');
                    });
                }
            }
            
            // Add index on marked_by and attendance_date if not exists
            if (!in_array('attendance_marked_date_idx', $indexNames)) {
                Schema::table('attendance', function (Blueprint $table) {
                    $table->index(['marked_by', 'attendance_date'], 'attendance_marked_date_idx');
                });
            }
        } catch (\Exception $e) {
            \Log::info('Index creation error (might already exist): ' . $e->getMessage());
        }

        // ============================================
        // FIX 5: Update Timetables Status Enum
        // ============================================
        
        try {
            $columnType = DB::select("SHOW COLUMNS FROM timetables WHERE Field = 'status'");
            
            if (!empty($columnType) && isset($columnType[0]->Type) && str_contains($columnType[0]->Type, 'enum')) {
                // Extract current enum values
                preg_match('/enum\((.*)\)/', $columnType[0]->Type, $matches);
                $currentValues = array_map('trim', explode(',', str_replace("'", "", $matches[1])));
                
                // Add new status values if not present
                $newValues = ['upcoming', 'active', 'closed', 'open'];
                foreach ($newValues as $value) {
                    if (!in_array($value, $currentValues)) {
                        $currentValues[] = $value;
                    }
                }
                
                // Build the new enum string
                $enumString = "ENUM('" . implode("','", $currentValues) . "')";
                
                // Modify the column
                DB::statement("ALTER TABLE timetables MODIFY COLUMN status {$enumString} DEFAULT 'upcoming'");
                
                // Update existing timetables based on date
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');
                
                // Past dates → 'closed'
                DB::table('timetables')
                    ->whereNotNull('date')
                    ->where('date', '<=', $yesterday)
                    ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
                    ->update(['status' => 'closed']);
                
                // Today → 'active'
                DB::table('timetables')
                    ->whereNotNull('date')
                    ->where('date', $today)
                    ->whereNotIn('status', ['active', 'cancelled', 'completed'])
                    ->update(['status' => 'active']);
                
                // Future dates → 'upcoming'
                DB::table('timetables')
                    ->whereNotNull('date')
                    ->where('date', '>', $today)
                    ->whereNotIn('status', ['upcoming', 'cancelled', 'completed'])
                    ->update(['status' => 'upcoming']);
            }
        } catch (\Exception $e) {
            \Log::info('Timetables status update error: ' . $e->getMessage());
        }

        // Add status index to timetables if not exists
        try {
            $hasStatusIndex = collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_status_idx'"))->first();
            if (!$hasStatusIndex) {
                Schema::table('timetables', function (Blueprint $table) {
                    $table->index('status', 'timetables_status_idx');
                });
            }
        } catch (\Exception $e) {
            \Log::info('Timetables status index error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop added indexes
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('attendance_student_date_idx');
            $table->dropIndex('attendance_timetable_date_idx');
            $table->dropIndex('attendance_marked_date_idx');
        });

        // Drop added columns
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'timetable_id')) {
                $table->dropForeign(['timetable_id']);
                $table->dropColumn('timetable_id');
            }
            
            if (Schema::hasColumn('attendance', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            
            if (Schema::hasColumn('attendance', 'check_out_time')) {
                $table->dropColumn('check_out_time');
            }
        });

        // Note: We don't revert the status enum changes to avoid data loss
        \Log::info('Migration rollback complete, but status enum changes preserved to prevent data loss');
    }
};
