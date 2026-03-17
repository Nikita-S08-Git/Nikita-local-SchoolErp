<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Academic\Division;
use App\Models\Academic\Program;

echo "=== Database Check ===" . PHP_EOL;
echo "Programs count: " . Program::count() . PHP_EOL;
echo "Divisions count: " . Division::count() . PHP_EOL;
echo PHP_EOL;

echo "All Divisions:" . PHP_EOL;
$divisions = Division::with('program')->get();
foreach ($divisions as $div) {
    echo "- {$div->division_name} (Program: " . ($div->program->name ?? 'NULL') . ") - Active: " . ($div->is_active ? 'Yes' : 'No') . PHP_EOL;
}
