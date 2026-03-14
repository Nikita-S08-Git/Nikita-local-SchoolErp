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
     * Adds 'upcoming', 'active', and 'closed' status values to the timetables table
     * for automatic date-based status management.
     * 
     * Status Logic:
     * - Past date: 'closed'
     * - Today: 'active'
     * - Future: 'upcoming'
     */
    public function up(): void
    {
        // Check current enum values and modify to include new values
        try {
            $columnType = DB::select("SHOW COLUMNS FROM timetables WHERE Field = 'status'");
            
            if (!empty($columnType) && str_contains($columnType[0]->Type, 'enum')) {
                // Extract current enum values
                preg_match('/enum\((.*)\)/', $columnType[0]->Type, $matches);
                $currentValues = explode(',', str_replace("'", "", $matches[1]));
                
                // Add new status values
                $newValues = array_merge($currentValues, ['upcoming', 'active', 'closed', 'open']);
                $newValues = array_unique($newValues);
                
                // Build the new enum string
                $enumString = "ENUM('" . implode("','", $newValues) . "')";
                
                // Modify the column
                DB::statement("ALTER TABLE timetables MODIFY COLUMN status {$enumString} DEFAULT 'upcoming'");
                
                echo "Updated timetables status column.\n";
            }
        } catch (\Exception $e) {
            echo "Error updating timetables status column: " . $e->getMessage() . "\n";
        }
        
        // Add index for status if not exists
        try {
            $hasStatusIndex = collect(DB::select("SHOW INDEX FROM timetables WHERE Key_name = 'timetables_status_idx'"))->first();
            if (!$hasStatusIndex) {
                Schema::table('timetables', function (Blueprint $table) {
                    $table->index('status', 'timetables_status_idx');
                });
            }
        } catch (\Exception $e) {
            echo "Error adding status index: " . $e->getMessage() . "\n";
        }
        
        // UPDATE EXISTING TIMETABLES based on date
        try {
            $today = now()->format('Y-m-d');
            $yesterday = now()->subDay()->format('Y-m-d');
            
            // Update timetables with past dates to 'closed'
            $closedCount = DB::table('timetables')
                ->whereNotNull('date')
                ->where('date', '<=', $yesterday)
                ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
                ->update(['status' => 'closed']);
            
            echo "Updated {$closedCount} timetables to 'closed'.\n";
            
            // Update timetables with today's date to 'active'
            $activeCount = DB::table('timetables')
                ->whereNotNull('date')
                ->where('date', $today)
                ->whereNotIn('status', ['active', 'cancelled', 'completed'])
                ->update(['status' => 'active']);
            
            echo "Updated {$activeCount} timetables to 'active'.\n";
            
            // Update timetables with future dates to 'upcoming'
            $upcomingCount = DB::table('timetables')
                ->whereNotNull('date')
                ->where('date', '>', $today)
                ->whereNotIn('status', ['upcoming', 'cancelled', 'completed'])
                ->update(['status' => 'upcoming']);
            
            echo "Updated {$upcomingCount} timetables to 'upcoming'.\n";
            
        } catch (\Exception $e) {
            echo "Error updating existing timetables: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE timetables MODIFY COLUMN status ENUM('active','cancelled','completed','open','closed','upcoming') DEFAULT 'active'");
        } catch (\Exception $e) {
            echo "Error reverting timetables status column: " . $e->getMessage() . "\n";
        }
    }
};
