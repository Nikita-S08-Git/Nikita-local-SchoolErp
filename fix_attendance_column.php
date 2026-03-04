<?php
/**
 * Standalone script to add academic_session_id column to attendance table
 * Run via: php fix_attendance_column.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking attendance table structure...\n";

// Check if academic_session_id column exists
if (Schema::hasColumn('attendance', 'academic_session_id')) {
    echo "Column 'academic_session_id' already exists in attendance table.\n";
    exit(0);
}

// Add the column
try {
    Schema::table('attendance', function ($table) {
        $table->foreignId('academic_session_id')
            ->after('division_id')
            ->nullable()
            ->constrained('academic_sessions')
            ->onDelete('cascade');
    });
    echo "Successfully added 'academic_session_id' column to attendance table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
