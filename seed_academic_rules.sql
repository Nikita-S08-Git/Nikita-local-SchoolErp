-- SQL to seed Academic Rules with all categories
-- Run this in your MySQL database (e.g., via phpMyAdmin)

INSERT INTO academic_rules (rule_code, name, description, category, value_type, value, default_value, min_value, max_value, priority, display_order, is_active, is_mandatory, created_at, updated_at) 
SELECT * FROM (
    -- RESULT CATEGORY
    SELECT 'PASS_PERCENTAGE' as rule_code, 'Pass Percentage' as name, 'Minimum percentage required to pass a subject' as description, 'result' as category, 'decimal' as value_type, '40' as value, '40' as default_value, '0' as min_value, '100' as max_value, 10 as priority, 1 as display_order, 1 as is_active, 1 as is_mandatory, NOW() as created_at, NOW() as updated_at
    UNION ALL
    SELECT 'GRACE_MARKS', 'Grace Marks', 'Maximum grace marks that can be awarded to borderline students', 'result', 'integer', '5', '5', '0', '15', 20, 2, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'EXAM_MIN_THEORY', 'Minimum Theory Marks', 'Minimum marks required in theory paper', 'result', 'integer', '35', '35', '0', '100', 30, 3, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'EXAM_MIN_PRACTICAL', 'Minimum Practical Marks', 'Minimum marks required in practical exam', 'result', 'integer', '20', '20', '0', '100', 40, 4, 1, 0, NOW(), NOW()
    
    UNION ALL
    -- ATTENDANCE CATEGORY
    SELECT 'MIN_ATTENDANCE', 'Minimum Attendance %', 'Minimum attendance required to be eligible for exams', 'attendance', 'integer', '75', '75', '50', '100', 10, 1, 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'ATTENDANCE_GRACE', 'Attendance Grace %', 'Grace percentage for attendance condonation', 'attendance', 'integer', '5', '5', '0', '20', 20, 2, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'MEDICAL_LEAVE_EXEMPT', 'Medical Leave Exemption', 'Allow medical leave exemption from attendance', 'attendance', 'boolean', '1', '1', NULL, NULL, 30, 3, 1, 0, NOW(), NOW()
    
    UNION ALL
    -- ATKT CATEGORY
    SELECT 'ATKT_MAX_SUBJECTS', 'Maximum ATKT Subjects', 'Maximum number of failed subjects allowed for ATKT', 'atkt', 'integer', '3', '3', '0', '10', 10, 1, 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'ATKT_MAX_ATTEMPTS', 'Maximum ATKT Attempts', 'Maximum number of attempts allowed to clear ATKT', 'atkt', 'integer', '3', '3', '1', '5', 20, 2, 1, 1, NOW(), NOW()
    UNION ALL
    SELECT 'ATKT_FEE_PER_SUBJECT', 'ATKT Fee Per Subject', 'Fee per ATKT subject examination', 'atkt', 'integer', '500', '500', '0', '5000', 30, 3, 1, 0, NOW(), NOW()
    
    UNION ALL
    -- PROMOTION CATEGORY
    SELECT 'FEE_CLEARANCE_REQUIRED', 'Fee Clearance Required', 'Fee clearance mandatory for promotion', 'promotion', 'boolean', '0', '0', NULL, NULL, 10, 1, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'PROMOTION_MIN_ATTENDANCE', 'Promotion Min Attendance', 'Minimum attendance for class promotion', 'promotion', 'integer', '75', '75', '50', '100', 20, 2, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'YEAR_BACK_CONDITION', 'Year Back Condition', 'Student with X ATKT gets year back', 'promotion', 'integer', '6', '6', '1', '10', 30, 3, 1, 0, NOW(), NOW()
    
    UNION ALL
    -- FEE CATEGORY
    SELECT 'LATE_FEE_FINE', 'Late Fee Fine', 'Fine amount for late fee payment', 'fee', 'integer', '100', '100', '0', '5000', 10, 1, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'FEE_INSTALLMENT_ALLOWED', 'Fee Installments Allowed', 'Allow fee payment in installments', 'fee', 'boolean', '1', '1', NULL, NULL, 20, 2, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'MAX_INSTALLMENTS', 'Maximum Installments', 'Maximum number of fee installments', 'fee', 'integer', '4', '4', '1', '10', 30, 3, 1, 0, NOW(), NOW()
    
    UNION ALL
    -- EXAMINATION CATEGORY
    SELECT 'EXAM_FORM_FEE', 'Examination Form Fee', 'Fee for examination form submission', 'examination', 'integer', '500', '500', '0', '10000', 10, 1, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'REVALUATION_FEE', 'Revaluation Fee', 'Fee for paper revaluation', 'examination', 'integer', '1000', '1000', '0', '5000', 20, 2, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'PHOTO_COPY_FEE', 'Answer Copy Fee', 'Fee for getting photo copy of answer sheet', 'examination', 'integer', '300', '300', '0', '2000', 30, 3, 1, 0, NOW(), NOW()
    
    UNION ALL
    -- GENERAL CATEGORY
    SELECT 'ACADEMIC_YEAR_START', 'Academic Year Start Month', 'Month when academic year starts (1-12)', 'general', 'integer', '4', '4', '1', '12', 10, 1, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'MAX_STUDENTS_CLASS', 'Max Students Per Class', 'Maximum students allowed in a division', 'general', 'integer', '60', '60', '10', '100', 20, 2, 1, 0, NOW(), NOW()
    UNION ALL
    SELECT 'AUTO_DIVISION_ALLOT', 'Auto Division Allotment', 'Automatically allot divisions to new admissions', 'general', 'boolean', '1', '1', NULL, NULL, 30, 3, 1, 0, NOW(), NOW()
) AS new_rules
WHERE NOT EXISTS (
    SELECT 1 FROM academic_rules WHERE rule_code = new_rules.rule_code
);
