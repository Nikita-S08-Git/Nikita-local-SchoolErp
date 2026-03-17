<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration to ensure timetables day_of_week values are lowercase
 * 
 * This ensures any existing data with capitalized day names gets converted to lowercase
 * to match the database enum constraint.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert any capitalized day names to lowercase
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($days as $day) {
            DB::table('timetables')
                ->where('day_of_week', $day)
                ->update(['day_of_week' => strtolower($day)]);
        }
        
        // Also handle any mixed case variations
        DB::statement("UPDATE timetables SET day_of_week = LOWER(day_of_week) WHERE day_of_week IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally convert back to capitalized (for display purposes only - not needed)
    }
};
