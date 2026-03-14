<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add more examination types to the ENUM
     */
    public function up(): void
    {
        // Get current enum values
        $result = DB::select("SHOW COLUMNS FROM examinations WHERE Field = 'type'");
        $typeColumn = $result[0] ?? null;
        
        if ($typeColumn && isset($typeColumn->Type)) {
            // Extract current enum values
            preg_match("/enum\('(.*)'\)/", $typeColumn->Type, $matches);
            $currentValues = explode("','", $matches[1] ?? '');
            
            // Add new values if they don't exist
            $newValues = ['internal', 'external', 'practical', 'midterm', 'final', 'unit_test', 'quiz', 'semester'];
            $valuesToAdd = array_diff($newValues, $currentValues);
            
            if (!empty($valuesToAdd)) {
                $allValues = array_merge($currentValues, $valuesToAdd);
                $enumString = implode("','", $allValues);
                
                DB::statement("ALTER TABLE examinations MODIFY COLUMN type ENUM('$enumString') NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE examinations MODIFY COLUMN type ENUM('internal', 'external', 'practical') NOT NULL");
    }
};
