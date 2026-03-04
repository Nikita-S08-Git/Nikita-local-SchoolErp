<?php
/**
 * Web-accessible script to add academic_session_id column to attendance table
 * Access via: http://127.0.0.1:8000/fix_attendance.php
 * IMPORTANT: Delete this file after use for security!
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');

echo "Checking attendance table structure...\n";

// Check if academic_session_id column exists
if (Schema::hasColumn('attendance', 'academic_session_id')) {
    echo "Column 'academic_session_id' already exists in attendance table.\n";
    echo "Done! Delete this file for security.\n";
    exit(0);
}

// Check if division_id exists
if (!Schema::hasColumn('attendance', 'division_id')) {
    echo "Column 'division_id' doesn't exist either. Adding both columns...\n";
    
    Schema::table('attendance', function ($table) {
        $table->foreignId('division_id')->nullable()->constrained()->onDelete('cascade');
    });
}

// Add the academic_session_id column
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

echo "Done! Delete this file for security.\n";
