<?php
/**
 * Quick script to seed Academic Rules
 * Run: php seed_academic_rules.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Define the rules to insert
$rules = [
    // RESULT RULES
    [
        'rule_code' => 'PASS_PERCENTAGE',
        'name' => 'Pass Percentage',
        'description' => 'Minimum percentage required to pass a subject',
        'category' => 'result',
        'value_type' => 'decimal',
        'value' => '40',
        'default_value' => '40',
        'min_value' => '0',
        'max_value' => '100',
        'priority' => 10,
        'display_order' => 1,
        'is_active' => true,
        'is_mandatory' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'rule_code' => 'GRACE_MARKS',
        'name' => 'Grace Marks',
        'description' => 'Maximum grace marks that can be awarded to borderline students',
        'category' => 'result',
        'value_type' => 'integer',
        'value' => '5',
        'default_value' => '5',
        'min_value' => '0',
        'max_value' => '15',
        'priority' => 20,
        'display_order' => 2,
        'is_active' => true,
        'is_mandatory' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // ATTENDANCE RULES
    [
        'rule_code' => 'MIN_ATTENDANCE',
        'name' => 'Minimum Attendance Percentage',
        'description' => 'Minimum attendance required to be eligible for exams',
        'category' => 'attendance',
        'value_type' => 'integer',
        'value' => '75',
        'default_value' => '75',
        'min_value' => '50',
        'max_value' => '100',
        'priority' => 10,
        'display_order' => 1,
        'is_active' => true,
        'is_mandatory' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'rule_code' => 'ATTENDANCE_GRACE',
        'name' => 'Attendance Grace Percentage',
        'description' => 'Grace percentage for attendance condonation',
        'category' => 'attendance',
        'value_type' => 'integer',
        'value' => '5',
        'default_value' => '5',
        'min_value' => '0',
        'max_value' => '20',
        'priority' => 20,
        'display_order' => 2,
        'is_active' => true,
        'is_mandatory' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // ATKT RULES
    [
        'rule_code' => 'ATKT_MAX_SUBJECTS',
        'name' => 'Maximum ATKT Subjects',
        'description' => 'Maximum number of failed subjects allowed for ATKT',
        'category' => 'atkt',
        'value_type' => 'integer',
        'value' => '3',
        'default_value' => '3',
        'min_value' => '0',
        'max_value' => '10',
        'priority' => 10,
        'display_order' => 1,
        'is_active' => true,
        'is_mandatory' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'rule_code' => 'ATKT_MAX_ATTEMPTS',
        'name' => 'Maximum ATKT Attempts',
        'description' => 'Maximum number of attempts allowed to clear ATKT',
        'category' => 'atkt',
        'value_type' => 'integer',
        'value' => '3',
        'default_value' => '3',
        'min_value' => '1',
        'max_value' => '5',
        'priority' => 20,
        'display_order' => 2,
        'is_active' => true,
        'is_mandatory' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    // PROMOTION RULES
    [
        'rule_code' => 'FEE_CLEARANCE_REQUIRED',
        'name' => 'Fee Clearance Required for Promotion',
        'description' => 'Whether fee clearance is mandatory for promotion',
        'category' => 'promotion',
        'value_type' => 'boolean',
        'value' => '0',
        'default_value' => '0',
        'priority' => 10,
        'display_order' => 1,
        'is_active' => true,
        'is_mandatory' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

$inserted = 0;
foreach ($rules as $rule) {
    // Check if exists
    $exists = DB::table('academic_rules')->where('rule_code', $rule['rule_code'])->first();
    if (!$exists) {
        DB::table('academic_rules')->insert($rule);
        $inserted++;
        echo "Inserted: " . $rule['rule_code'] . "\n";
    } else {
        echo "Already exists: " . $rule['rule_code'] . "\n";
    }
}

echo "\nDone! $inserted new rules inserted.\n";
