<?php

/**
 * Script to fix and verify the timetables table structure
 * Run with: php fix_timetable_table.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Timetable Table Fix Script ===\n\n";

// Check if timetables table exists
if (!Schema::hasTable('timetables')) {
    echo "ERROR: timetables table does not exist!\n";
    echo "Please run migrations first: php artisan migrate\n";
    exit(1);
}

// Get current columns
$columns = Schema::getColumnListing('timetables');
echo "Current columns in timetables table:\n";
foreach ($columns as $column) {
    echo "  - $column\n";
}
echo "\n";

// Required columns for the timetable system
$requiredColumns = [
    'id', 'division_id', 'subject_id', 'teacher_id', 'room_id',
    'day_of_week', 'date', 'start_time', 'end_time',
    'period_name', 'room_number', 'academic_year_id',
    'is_break_time', 'is_active', 'status', 'notes',
    'created_at', 'updated_at', 'deleted_at'
];

$missingColumns = [];
foreach ($requiredColumns as $col) {
    if (!in_array($col, $columns)) {
        $missingColumns[] = $col;
    }
}

if (!empty($missingColumns)) {
    echo "WARNING: Missing columns detected:\n";
    foreach ($missingColumns as $col) {
        echo "  - $col\n";
    }
    echo "\n";
    echo "Please run the new migration to fix:\n";
    echo "  php artisan migrate\n";
} else {
    echo "SUCCESS: All required columns exist!\n";
}

// Check for related tables
echo "\n=== Checking Related Tables ===\n";

$tables = ['divisions', 'subjects', 'rooms', 'academic_years', 'time_slots'];
foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    echo "  $table: " . ($exists ? "EXISTS" : "MISSING") . "\n";
}

// Check for sample data
echo "\n=== Checking Sample Data ===\n";

$divisionCount = DB::table('divisions')->count();
echo "  Divisions: $divisionCount\n";

$subjectCount = DB::table('subjects')->count();
echo "  Subjects: $subjectCount\n";

$teacherCount = DB::table('users')
    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->where('roles.name', 'teacher')
    ->count();
echo "  Teachers: $teacherCount\n";

$roomCount = DB::table('rooms')->count();
echo "  Rooms: $roomCount\n";

$academicYearCount = DB::table('academic_years')->count();
echo "  Academic Years: $academicYearCount\n";

$timeSlotCount = DB::table('time_slots')->count();
echo "  Time Slots: $timeSlotCount\n";

if ($divisionCount === 0 || $subjectCount === 0 || $teacherCount === 0) {
    echo "\nWARNING: Missing essential data for creating timetables!\n";
    echo "Please seed the database with required data.\n";
}

echo "\n=== Done ===\n";
