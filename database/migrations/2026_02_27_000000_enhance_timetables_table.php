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
     * This migration adds:
     * - room_id foreign key
     * - status field (active/cancelled)
     * - Additional indexes for performance
     * - Unique constraints to prevent conflicts
     */
    public function up(): void
    {
        // Add room_id column first
        if (!Schema::hasColumn('timetables', 'room_id')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->foreignId('room_id')
                    ->nullable()
                    ->constrained('rooms')
                    ->onDelete('set null')
                    ->after('teacher_id');
            });
        }

        // Add status column (replacing is_active with more detailed status)
        if (!Schema::hasColumn('timetables', 'status')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->enum('status', ['active', 'cancelled', 'completed'])
                    ->default('active')
                    ->after('is_active');
            });
        }

        // Add notes column for additional information
        if (!Schema::hasColumn('timetables', 'notes')) {
            Schema::table('timetables', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('status');
            });
        }

        // Add indexes for performance
        Schema::table('timetables', function (Blueprint $table) {
            // Existing indexes check and add if not exists
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_day_of_week_index'"))->first()) {
                $table->index('day_of_week', 'timetables_day_of_week_index');
            }
            
            // Composite index for common queries
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_division_day_index'"))->first()) {
                $table->index(['division_id', 'day_of_week', 'start_time'], 'timetables_division_day_index');
            }
            
            // Teacher conflict detection index
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_teacher_day_index'"))->first()) {
                $table->index(['teacher_id', 'day_of_week', 'start_time'], 'timetables_teacher_day_index');
            }
            
            // Room conflict detection index
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_room_day_index'"))->first()) {
                $table->index(['room_id', 'day_of_week', 'start_time'], 'timetables_room_day_index');
            }
            
            // Status index for filtering
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_status_index'"))->first()) {
                $table->index('status', 'timetables_status_index');
            }
        });

        // Add unique constraint for teacher conflict (same teacher, same day, overlapping time)
        // Note: This is a soft constraint - we'll handle this in application logic
        // because overlapping time ranges are complex to handle with simple unique constraints

        // Create a helper view/table for tracking conflicts if needed
        // This will be handled by the application logic in the Timetable model
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('timetables', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropColumn('room_id');
            }
            
            if (Schema::hasColumn('timetables', 'status')) {
                $table->dropColumn('status');
            }
            
            if (Schema::hasColumn('timetables', 'notes')) {
                $table->dropColumn('notes');
            }

            // Drop custom indexes
            $table->dropIndex('timetables_day_of_week_index');
            $table->dropIndex('timetables_division_day_index');
            $table->dropIndex('timetables_teacher_day_index');
            $table->dropIndex('timetables_room_day_index');
            $table->dropIndex('timetables_status_index');
        });
    }
};
