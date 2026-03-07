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
     * Update existing attendance table to use proper structure:
     * - Rename attendance_date to date (if not already renamed)
     * - Add ip_address field if not exists
     * - Ensure proper indexes exist
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Rename attendance_date to date if it exists
            if (DB::getSchemaBuilder()->hasColumn('attendance', 'attendance_date')) {
                $table->renameColumn('attendance_date', 'date');
            }

            // Add ip_address if not exists
            if (!DB::getSchemaBuilder()->hasColumn('attendance', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('remarks');
            }

            // Add indexes if they don't exist
            $indexes = DB::select("SHOW INDEX FROM attendance");
            $indexNames = array_column($indexes, 'Key_name');
            
            if (!in_array('attendance_date_student_id_index', $indexNames) && !in_array('date_student_id_index', $indexNames)) {
                $table->index(['date', 'student_id']);
            }
            
            if (DB::getSchemaBuilder()->hasColumn('attendance', 'timetable_id')) {
                if (!in_array('attendance_timetable_id_date_index', $indexNames) && !in_array('timetable_id_date_index', $indexNames)) {
                    $table->index(['timetable_id', 'date']);
                }
            }
            
            if (!in_array('attendance_marked_by_date_index', $indexNames) && !in_array('marked_by_date_index', $indexNames)) {
                $table->index(['marked_by', 'date']);
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Check if column is named 'date' and rename back
            if (DB::getSchemaBuilder()->hasColumn('attendance', 'date') && !DB::getSchemaBuilder()->hasColumn('attendance', 'attendance_date')) {
                $table->renameColumn('date', 'attendance_date');
            }
            
            if (DB::getSchemaBuilder()->hasColumn('attendance', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            
            $table->dropIndex(['date', 'student_id']);
            $table->dropIndex(['timetable_id', 'date']);
            $table->dropIndex(['marked_by', 'date']);
        });
    }
};
