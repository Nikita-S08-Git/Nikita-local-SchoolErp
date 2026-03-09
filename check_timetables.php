<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Timetable Data ===\n\n";

$timetables = App\Models\Academic\Timetable::with(['division', 'subject', 'teacher'])->get();

if ($timetables->isEmpty()) {
    echo "❌ No timetables found in database!\n";
    echo "Please run: php artisan db:seed --class=DetailedTimetableSeeder\n";
} else {
    echo "Total timetables: " . $timetables->count() . "\n\n";
    
    foreach ($timetables as $timetable) {
        echo "ID: " . $timetable->id . "\n";
        echo "  Division: " . ($timetable->division ? $timetable->division->division_name : 'N/A') . "\n";
        echo "  Subject: " . ($timetable->subject ? $timetable->subject->name : 'N/A') . "\n";
        echo "  Teacher: " . ($timetable->teacher ? $timetable->teacher->name : 'N/A') . " (ID: " . $timetable->teacher_id . ")\n";
        echo "  Day: " . $timetable->day_of_week . "\n";
        echo "  Time: " . $timetable->start_time . " - " . $timetable->end_time . "\n";
        echo "\n";
    }
    
    echo "=== Teacher Info ===\n";
    $teachers = App\Models\User::role('teacher')->get();
    echo "Total teachers: " . $teachers->count() . "\n";
    foreach ($teachers as $teacher) {
        echo "  ID: " . $teacher->id . " - " . $teacher->name . " (" . $teacher->email . ")\n";
    }
}
