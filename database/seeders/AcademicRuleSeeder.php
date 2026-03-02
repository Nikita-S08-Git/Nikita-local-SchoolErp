<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\AcademicRule;

/**
 * Academic Rule Seeder
 *
 * Seeds the academic_rules table with default institutional rules.
 * These rules define pass/fail/ATKT criteria and other academic policies.
 */
class AcademicRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $rules = [
            // ========================================
            // RESULT RULES
            // ========================================
            [
                'rule_code' => AcademicRule::RULE_PASS_PERCENTAGE,
                'name' => 'Pass Percentage',
                'description' => 'Minimum percentage required to pass a subject',
                'category' => AcademicRule::CATEGORY_RESULT,
                'value_type' => AcademicRule::VALUE_TYPE_DECIMAL,
                'value' => '40',
                'default_value' => '40',
                'min_value' => '0',
                'max_value' => '100',
                'priority' => 10,
                'display_order' => 1,
                'is_active' => true,
                'is_mandatory' => true,
                'tags' => ['result', 'passing', 'percentage'],
            ],
            [
                'rule_code' => AcademicRule::RULE_GRACE_MARKS,
                'name' => 'Grace Marks',
                'description' => 'Maximum grace marks that can be awarded to borderline students',
                'category' => AcademicRule::CATEGORY_RESULT,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '5',
                'default_value' => '5',
                'min_value' => '0',
                'max_value' => '15',
                'priority' => 20,
                'display_order' => 2,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['result', 'grace', 'bonus'],
            ],

            // ========================================
            // ATTENDANCE RULES
            // ========================================
            [
                'rule_code' => AcademicRule::RULE_MIN_ATTENDANCE,
                'name' => 'Minimum Attendance Percentage',
                'description' => 'Minimum attendance required to be eligible for exams',
                'category' => AcademicRule::CATEGORY_ATTENDANCE,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '75',
                'default_value' => '75',
                'min_value' => '50',
                'max_value' => '100',
                'priority' => 10,
                'display_order' => 1,
                'is_active' => true,
                'is_mandatory' => true,
                'tags' => ['attendance', 'eligibility', 'exam'],
            ],
            [
                'rule_code' => AcademicRule::RULE_ATTENDANCE_GRACE,
                'name' => 'Attendance Grace Percentage',
                'description' => 'Grace percentage for attendance condonation',
                'category' => AcademicRule::CATEGORY_ATTENDANCE,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '5',
                'default_value' => '5',
                'min_value' => '0',
                'max_value' => '20',
                'priority' => 20,
                'display_order' => 2,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['attendance', 'grace', 'condonation'],
            ],

            // ========================================
            // ATKT RULES
            // ========================================
            [
                'rule_code' => AcademicRule::RULE_ATKT_MAX_SUBJECTS,
                'name' => 'Maximum ATKT Subjects',
                'description' => 'Maximum number of failed subjects allowed for ATKT',
                'category' => AcademicRule::CATEGORY_ATKT,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '3',
                'default_value' => '3',
                'min_value' => '0',
                'max_value' => '10',
                'priority' => 10,
                'display_order' => 1,
                'is_active' => true,
                'is_mandatory' => true,
                'tags' => ['atkt', 'backlog', 'promotion'],
            ],
            [
                'rule_code' => AcademicRule::RULE_ATKT_MAX_ATTEMPTS,
                'name' => 'Maximum ATKT Attempts',
                'description' => 'Maximum number of attempts allowed to clear ATKT',
                'category' => AcademicRule::CATEGORY_ATKT,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '3',
                'default_value' => '3',
                'min_value' => '1',
                'max_value' => '5',
                'priority' => 20,
                'display_order' => 2,
                'is_active' => true,
                'is_mandatory' => true,
                'tags' => ['atkt', 'attempts', 'backlog'],
            ],

            // ========================================
            // PROMOTION RULES
            // ========================================
            [
                'rule_code' => AcademicRule::RULE_FEE_CLEARANCE_REQUIRED,
                'name' => 'Fee Clearance Required for Promotion',
                'description' => 'Whether fee clearance is mandatory for promotion',
                'category' => AcademicRule::CATEGORY_PROMOTION,
                'value_type' => AcademicRule::VALUE_TYPE_BOOLEAN,
                'value' => '0',
                'default_value' => '0',
                'priority' => 10,
                'display_order' => 1,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['promotion', 'fee', 'clearance'],
            ],
            [
                'rule_code' => 'PROMOTION_MIN_ATTENDANCE',
                'name' => 'Minimum Attendance for Promotion',
                'description' => 'Minimum attendance required to be eligible for promotion',
                'category' => AcademicRule::CATEGORY_PROMOTION,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '75',
                'default_value' => '75',
                'min_value' => '50',
                'max_value' => '100',
                'priority' => 20,
                'display_order' => 2,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['promotion', 'attendance', 'eligibility'],
            ],

            // ========================================
            // EXAMINATION RULES
            // ========================================
            [
                'rule_code' => 'EXAM_MIN_MARKS',
                'name' => 'Minimum Marks per Subject',
                'description' => 'Minimum marks required in each subject theory',
                'category' => AcademicRule::CATEGORY_EXAMINATION,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '40',
                'default_value' => '40',
                'min_value' => '0',
                'max_value' => '100',
                'priority' => 10,
                'display_order' => 1,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['examination', 'minimum', 'marks'],
            ],
            [
                'rule_code' => 'EXAM_AGGREGATE_REQUIRED',
                'name' => 'Minimum Aggregate Percentage',
                'description' => 'Minimum aggregate percentage required to pass examination',
                'category' => AcademicRule::CATEGORY_EXAMINATION,
                'value_type' => AcademicRule::VALUE_TYPE_DECIMAL,
                'value' => '40',
                'default_value' => '40',
                'min_value' => '0',
                'max_value' => '100',
                'priority' => 20,
                'display_order' => 2,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['examination', 'aggregate', 'percentage'],
            ],

            // ========================================
            // GENERAL RULES
            // ========================================
            [
                'rule_code' => 'COMPULSORY_SUBJECTS',
                'name' => 'Compulsory Subjects',
                'description' => 'List of subject IDs that are compulsory (must pass)',
                'category' => AcademicRule::CATEGORY_GENERAL,
                'value_type' => AcademicRule::VALUE_TYPE_ARRAY,
                'value' => '[]',
                'default_value' => '[]',
                'priority' => 10,
                'display_order' => 1,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['compulsory', 'subjects', 'mandatory'],
            ],
            [
                'rule_code' => 'ACADEMIC_YEAR_START_MONTH',
                'name' => 'Academic Year Start Month',
                'description' => 'Month when academic year starts (1=January, 4=April, etc.)',
                'category' => AcademicRule::CATEGORY_GENERAL,
                'value_type' => AcademicRule::VALUE_TYPE_INTEGER,
                'value' => '4',
                'default_value' => '4',
                'min_value' => '1',
                'max_value' => '12',
                'priority' => 20,
                'display_order' => 2,
                'is_active' => true,
                'is_mandatory' => false,
                'tags' => ['academic', 'year', 'month'],
            ],
        ];

        foreach ($rules as $ruleData) {
            AcademicRule::updateOrCreate(
                ['rule_code' => $ruleData['rule_code']],
                $ruleData
            );
        }

        $this->command->info('Academic rules seeded successfully: ' . count($rules) . ' rules created.');
    }
}
