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
     * This migration ensures all required columns exist in the timetables table.
     * It's a safety check to ensure the database schema matches what the application expects.
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Add academic_year_id if it doesn't exist
            if (!Schema::hasColumn('timetables', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('cascade')->after('teacher_id');
                $table->index(['academic_year_id', 'is_active']);
            }
            
            // Add is_break_time if it doesn't exist
            if (!Schema::hasColumn('timetables', 'is_break_time')) {
                $table->boolean('is_break_time')->default(false)->after('academic_year_id');
            }
            
            // Add period_name if it doesn't exist
            if (!Schema::hasColumn('timetables', 'period_name')) {
                $table->string('period_name', 50)->nullable()->after('end_time');
            }
            
            // Add room_number if it doesn't exist
            if (!Schema::hasColumn('timetables', 'room_number')) {
                $table->string('room_number', 50)->nullable()->after('period_name');
            }
            
            // Add room_id if it doesn't exist
            if (!Schema::hasColumn('timetables', 'room_id')) {
                $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null')->after('teacher_id');
            }
            
            // Add status if it doesn't exist
            if (!Schema::hasColumn('timetables', 'status')) {
                $table->enum('status', ['active', 'cancelled', 'completed'])->default('active')->after('is_active');
            }
            
            // Add notes if it doesn't exist
            if (!Schema::hasColumn('timetables', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            
            // Add date if it doesn't exist (for specific date scheduling)
            if (!Schema::hasColumn('timetables', 'date')) {
                $table->date('date')->nullable()->after('day_of_week');
            }
            
            // Add soft deletes if it doesn't exist
            if (!Schema::hasColumn('timetables', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add indexes for performance if they don't exist
        Schema::table('timetables', function (Blueprint $table) {
            // Check and add day_of_week index
            try {
                $hasDayIndex = collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_day_of_week_index'"))->first();
                if (!$hasDayIndex) {
                    $table->index('day_of_week', 'timetables_day_of_week_index');
                }
            } catch (\Exception $e) {
                // Index might already exist or table doesn't exist
            }
            
            // Check and add date index
            try {
                $hasDateIndex = collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_date_index'"))->first();
                if (!$hasDateIndex) {
                    $table->index('date', 'timetables_date_index');
                }
            } catch (\Exception $e) {
                // Index might already exist or table doesn't exist
            }
            
            // Check and add composite division + date index
            try {
                $hasDivDateIndex = collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_division_date_index'"))->first();
                if (!$hasDivDateIndex) {
                    $table->index(['division_id', 'date'], 'timetables_division_date_index');
                }
            } catch (\Exception $e) {
                // Index might already exist or table doesn't exist
            }
            
            // Check and add status index
            try {
                $hasStatusIndex = collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_status_index'"))->first();
                if (!$hasStatusIndex) {
                    $table->index('status', 'timetables_status_index');
                }
            } catch (\Exception $e) {
                // Index might already exist or table doesn't exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a safety migration, don't remove columns on rollback
    }
};
