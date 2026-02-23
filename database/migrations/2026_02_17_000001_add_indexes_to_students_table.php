<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Performance Indexes to Students Table
 * 
 * This migration adds database indexes to improve query performance
 * for frequently searched and filtered columns.
 * 
 * Benefits:
 * - Faster search queries
 * - Improved filter performance
 * - Better JOIN performance
 * - Reduced database load
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Indexes for unique identifiers (if not already indexed)
            if (!$this->indexExists('students', 'students_admission_number_index')) {
                $table->index('admission_number', 'students_admission_number_index');
            }
            
            if (!$this->indexExists('students', 'students_roll_number_index')) {
                $table->index('roll_number', 'students_roll_number_index');
            }
            
            if (!$this->indexExists('students', 'students_prn_index')) {
                $table->index('prn', 'students_prn_index');
            }
            
            // Indexes for foreign keys (improve JOIN performance)
            if (!$this->indexExists('students', 'students_program_id_index')) {
                $table->index('program_id', 'students_program_id_index');
            }
            
            if (!$this->indexExists('students', 'students_division_id_index')) {
                $table->index('division_id', 'students_division_id_index');
            }
            
            if (!$this->indexExists('students', 'students_academic_session_id_index')) {
                $table->index('academic_session_id', 'students_academic_session_id_index');
            }
            
            if (!$this->indexExists('students', 'students_user_id_index')) {
                $table->index('user_id', 'students_user_id_index');
            }
            
            // Indexes for frequently filtered columns
            if (!$this->indexExists('students', 'students_student_status_index')) {
                $table->index('student_status', 'students_student_status_index');
            }
            
            if (!$this->indexExists('students', 'students_academic_year_index')) {
                $table->index('academic_year', 'students_academic_year_index');
            }
            
            if (!$this->indexExists('students', 'students_gender_index')) {
                $table->index('gender', 'students_gender_index');
            }
            
            if (!$this->indexExists('students', 'students_category_index')) {
                $table->index('category', 'students_category_index');
            }
            
            if (!$this->indexExists('students', 'students_admission_date_index')) {
                $table->index('admission_date', 'students_admission_date_index');
            }
            
            // Composite indexes for common query combinations
            if (!$this->indexExists('students', 'students_program_status_index')) {
                $table->index(['program_id', 'student_status'], 'students_program_status_index');
            }
            
            if (!$this->indexExists('students', 'students_division_status_index')) {
                $table->index(['division_id', 'student_status'], 'students_division_status_index');
            }
            
            if (!$this->indexExists('students', 'students_program_year_index')) {
                $table->index(['program_id', 'academic_year'], 'students_program_year_index');
            }
            
            // Full-text index for name search (MySQL/MariaDB only)
            if (config('database.default') === 'mysql') {
                DB::statement('ALTER TABLE students ADD FULLTEXT INDEX students_name_fulltext (first_name, middle_name, last_name)');
            }
            
            // Index for email and mobile (for duplicate checking)
            if (!$this->indexExists('students', 'students_email_index')) {
                $table->index('email', 'students_email_index');
            }
            
            if (!$this->indexExists('students', 'students_mobile_number_index')) {
                $table->index('mobile_number', 'students_mobile_number_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop all indexes
            $indexes = [
                'students_admission_number_index',
                'students_roll_number_index',
                'students_prn_index',
                'students_program_id_index',
                'students_division_id_index',
                'students_academic_session_id_index',
                'students_user_id_index',
                'students_student_status_index',
                'students_academic_year_index',
                'students_gender_index',
                'students_category_index',
                'students_admission_date_index',
                'students_program_status_index',
                'students_division_status_index',
                'students_program_year_index',
                'students_email_index',
                'students_mobile_number_index',
            ];
            
            foreach ($indexes as $index) {
                if ($this->indexExists('students', $index)) {
                    $table->dropIndex($index);
                }
            }
            
            // Drop full-text index
            if (config('database.default') === 'mysql') {
                DB::statement('ALTER TABLE students DROP INDEX students_name_fulltext');
            }
        });
    }

    /**
     * Check if index exists
     * 
     * @param string $table
     * @param string $index
     * @return bool
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        if (config('database.default') === 'mysql') {
            $result = DB::select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$databaseName, $table, $index]
            );
            
            return $result[0]->count > 0;
        }
        
        // For SQLite
        if (config('database.default') === 'sqlite') {
            $result = DB::select("PRAGMA index_list({$table})");
            foreach ($result as $row) {
                if ($row->name === $index) {
                    return true;
                }
            }
        }
        
        return false;
    }
};
