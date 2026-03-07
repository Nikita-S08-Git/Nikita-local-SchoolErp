<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add date column to timetables table for specific date scheduling.
     * This allows both recurring (day_of_week) and specific date-based timetables.
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Add date column for specific date scheduling
            if (!Schema::hasColumn('timetables', 'date')) {
                $table->date('date')->nullable()->after('day_of_week');
            }
            
            // Add index for date-based queries
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_date_index'"))->first()) {
                $table->index('date', 'timetables_date_index');
            }
            
            // Add composite index for date + division queries
            if (!collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_division_date_index'"))->first()) {
                $table->index(['division_id', 'date'], 'timetables_division_date_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('timetables_date_index');
            $table->dropIndex('timetables_division_date_index');
            
            // Drop column
            if (Schema::hasColumn('timetables', 'date')) {
                $table->dropColumn('date');
            }
        });
    }
};
