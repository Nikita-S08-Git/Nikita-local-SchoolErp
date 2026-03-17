<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration to add foreign key constraints for data integrity
 * 
 * This ensures:
 * - ON DELETE RESTRICT: Prevents deletion of parent records with children
 * - ON DELETE CASCADE: Automatically deletes children when parent is deleted
 * - ON DELETE SET NULL: Sets foreign key to NULL when parent is deleted
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add foreign key for timetables.teacher_id -> users.id (RESTRICT)
        // This prevents deleting a teacher who has timetable entries
        if (Schema::hasColumn('timetables', 'teacher_id')) {
            Schema::table('timetables', function (Blueprint $table) {
                // Check if foreign key already exists
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'timetables' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'timetables_teacher_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('teacher_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('restrict');
                }
            });
        }

        // 2. Add foreign key for timetables.division_id -> divisions.id (CASCADE)
        if (Schema::hasColumn('timetables', 'division_id')) {
            Schema::table('timetables', function (Blueprint $table) {
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'timetables' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'timetables_division_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('division_id')
                        ->references('id')
                        ->on('divisions')
                        ->onDelete('cascade');
                }
            });
        }

        // 3. Add foreign key for timetables.subject_id -> subjects.id (RESTRICT)
        if (Schema::hasColumn('timetables', 'subject_id')) {
            Schema::table('timetables', function (Blueprint $table) {
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'timetables' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'timetables_subject_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('subject_id')
                        ->references('id')
                        ->on('subjects')
                        ->onDelete('restrict');
                }
            });
        }

        // 4. Add foreign key for attendance.student_id -> students.id (CASCADE)
        if (Schema::hasColumn('attendance', 'student_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'attendance' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'attendance_student_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('student_id')
                        ->references('id')
                        ->on('students')
                        ->onDelete('cascade');
                }
            });
        }

        // 5. Add foreign key for fees.student_id -> students.id (CASCADE)
        if (Schema::hasColumn('fees', 'student_id')) {
            Schema::table('fees', function (Blueprint $table) {
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'fees' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'fees_student_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('student_id')
                        ->references('id')
                        ->on('students')
                        ->onDelete('cascade');
                }
            });
        }

        // 6. Add foreign key for divisions.program_id -> programs.id (CASCADE)
        if (Schema::hasColumn('divisions', 'program_id')) {
            Schema::table('divisions', function (Blueprint $table) {
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'divisions' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'divisions_program_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('program_id')
                        ->references('id')
                        ->on('programs')
                        ->onDelete('cascade');
                }
            });
        }

        // 7. Add foreign key for divisions.academic_year_id -> academic_years.id (RESTRICT)
        if (Schema::hasColumn('divisions', 'academic_year_id')) {
            Schema::table('divisions', function (Blueprint $table) {
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'divisions' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND CONSTRAINT_NAME = 'divisions_academic_year_id_foreign'");
                
                if (empty($foreignKeys)) {
                    $table->foreign('academic_year_id')
                        ->references('id')
                        ->on('academic_years')
                        ->onDelete('restrict');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign keys (optional - usually not needed)
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['division_id']);
            $table->dropForeign(['subject_id']);
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['academic_year_id']);
        });
    }
};
