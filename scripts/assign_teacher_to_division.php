<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Academic\Division;
use App\Models\TeacherAssignment;

// Get teacher and first division
$teacher = User::where('email', 'teacher@schoolerp.com')->first();
$division = Division::first();

if (!$teacher) {
    echo "❌ Teacher not found\n";
    exit;
}

if (!$division) {
    echo "❌ No divisions exist. Please create one first.\n";
    exit;
}

// Create assignment
$assignment = TeacherAssignment::firstOrCreate(
    [
        'teacher_id' => $teacher->id,
        'division_id' => $division->id,
        'assignment_type' => 'division',
    ],
    [
        'is_primary' => true,
    ]
);

echo "✅ Teacher assigned to division!\n";
echo "   Teacher: {$teacher->name} ({$teacher->email})\n";
echo "   Division: {$division->division_name}\n";
echo "   Assignment Type: Division\n";
