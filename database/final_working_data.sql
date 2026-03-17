-- ============================================
-- SCHOOL ERP DATA - FOR EXISTING TABLES
-- Works with your existing table structure
-- All passwords: password
-- ============================================

USE schoolerp;

-- ============================================
-- CLEAR EXISTING DATA
-- ============================================
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM timetables;
DELETE FROM teacher_assignments;
DELETE FROM teacher_profiles;
DELETE FROM model_has_roles;
DELETE FROM users;
DELETE FROM holidays;
DELETE FROM subjects;
DELETE FROM divisions;
DELETE FROM programs;
DELETE FROM departments;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- INSERT DATA
-- ============================================

-- 1. DEPARTMENTS
INSERT INTO departments (id, name, code, description, is_active, created_at, updated_at) VALUES
(1, 'Commerce', 'COM', 'Department of Commerce', 1, NOW(), NOW()),
(2, 'Science', 'SCI', 'Department of Science', 1, NOW(), NOW()),
(3, 'Arts', 'ART', 'Department of Arts', 1, NOW(), NOW()),
(4, 'Management', 'MGT', 'Department of Management', 1, NOW(), NOW());

-- 2. PROGRAMS
INSERT INTO programs (id, name, code, duration_years, department_id, is_active, created_at, updated_at) VALUES
(1, 'Bachelor of Commerce', 'B.COM', 3, 1, 1, NOW(), NOW()),
(2, 'Bachelor of Science', 'B.SC', 3, 2, 1, NOW(), NOW()),
(3, 'Bachelor of Arts', 'B.A', 3, 3, 1, NOW(), NOW()),
(4, 'Master of Commerce', 'M.COM', 2, 1, 1, NOW(), NOW()),
(5, 'Master of Science', 'M.SC', 2, 2, 1, NOW(), NOW()),
(6, 'Master of Arts', 'M.A', 2, 3, 1, NOW(), NOW()),
(7, 'MBA', 'MBA', 2, 4, 1, NOW(), NOW()),
(8, 'BBA', 'BBA', 3, 4, 1, NOW(), NOW());

-- 3. ACADEMIC YEARS (Using correct column name)
INSERT INTO academic_years (id, name, start_date, end_date, is_active, created_at, updated_at) VALUES
(1, '2024-25', '2024-06-01', '2025-05-31', 1, NOW(), NOW()),
(2, '2025-26', '2025-06-01', '2026-05-31', 1, NOW(), NOW());

-- 4. ACADEMIC SESSIONS
INSERT INTO academic_sessions (id, session_name, start_date, end_date, is_active, created_at, updated_at) VALUES
(1, '2024-25', '2024-06-01', '2025-05-31', 1, NOW(), NOW()),
(2, '2025-26', '2025-06-01', '2026-05-31', 1, NOW(), NOW());

-- 5. DIVISIONS
INSERT INTO divisions (id, program_id, session_id, academic_year_id, division_name, max_students, is_active, created_at, updated_at) VALUES
(1, 1, 1, 1, 'A', 60, 1, NOW(), NOW()),
(2, 1, 1, 1, 'B', 60, 1, NOW(), NOW()),
(3, 1, 1, 1, 'C', 60, 1, NOW(), NOW()),
(4, 2, 1, 1, 'A', 60, 1, NOW(), NOW()),
(5, 2, 1, 1, 'B', 60, 1, NOW(), NOW()),
(6, 3, 1, 1, 'A', 60, 1, NOW(), NOW()),
(7, 4, 1, 1, 'A', 50, 1, NOW(), NOW()),
(8, 7, 1, 1, 'A', 60, 1, NOW(), NOW()),
(9, 8, 1, 1, 'A', 60, 1, NOW(), NOW()),
(10, 8, 1, 1, 'B', 60, 1, NOW(), NOW());

-- 6. SUBJECTS
INSERT INTO subjects (id, program_id, academic_year_id, name, code, credits, type, max_marks, passing_marks, is_active, created_at, updated_at) VALUES
(1, 1, 1, 'Financial Accounting', 'ACC101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(2, 1, 1, 'Business Management', 'BUS101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(3, 1, 1, 'Micro Economics', 'ECO101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(4, 1, 1, 'Business Mathematics', 'MAT101', 3, 'theory', 80, 35, 1, NOW(), NOW()),
(5, 1, 1, 'Statistics', 'STAT101', 3, 'theory', 80, 35, 1, NOW(), NOW()),
(6, 1, 1, 'Computer Applications', 'COM101', 3, 'both', 60, 25, 1, NOW(), NOW()),
(7, 1, 1, 'Business English', 'ENG101', 2, 'theory', 80, 35, 1, NOW(), NOW()),
(8, 1, 1, 'Business Law', 'LAW101', 3, 'theory', 80, 35, 1, NOW(), NOW()),
(9, 2, 1, 'Physics', 'PHY101', 4, 'both', 80, 35, 1, NOW(), NOW()),
(10, 2, 1, 'Chemistry', 'CHE101', 4, 'both', 80, 35, 1, NOW(), NOW()),
(11, 2, 1, 'Mathematics', 'MAT201', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(12, 2, 1, 'Biology', 'BIO101', 4, 'both', 80, 35, 1, NOW(), NOW()),
(13, 3, 1, 'Political Science', 'POL101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(14, 3, 1, 'History', 'HIS101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(15, 3, 1, 'Sociology', 'SOC101', 3, 'theory', 80, 35, 1, NOW(), NOW()),
(16, 3, 1, 'English Literature', 'ENG201', 3, 'theory', 80, 35, 1, NOW(), NOW()),
(17, 7, 1, 'Marketing Management', 'MKT101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(18, 7, 1, 'Human Resources', 'HR101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(19, 7, 1, 'Financial Management', 'FIN101', 4, 'theory', 80, 35, 1, NOW(), NOW()),
(20, 7, 1, 'Operations Management', 'OPS101', 3, 'theory', 80, 35, 1, NOW(), NOW());

-- 7. USERS (All passwords: password)
INSERT INTO users (id, name, email, email_verified_at, password, is_active, created_at, updated_at) VALUES
(1, 'Dr. Principal', 'principal@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(2, 'Prof. Teacher', 'teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(3, 'Prof. Class Teacher', 'class.teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(4, 'Prof. Commerce HOD', 'hod.commerce@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(5, 'Prof. Science HOD', 'hod.science@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(6, 'Prof. Math Teacher', 'math.teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(7, 'Prof. English Teacher', 'english.teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(8, 'Prof. Physics Teacher', 'physics.teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(9, 'Prof. Chemistry Teacher', 'chemistry.teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(10, 'Prof. Biology Teacher', 'biology.teacher@schoolerp.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW());

-- 8. ROLES ASSIGNMENT
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Models\\User', u.id
FROM users u
INNER JOIN roles r ON r.name = CASE u.email
    WHEN 'principal@schoolerp.com' THEN 'principal'
    WHEN 'teacher@schoolerp.com' THEN 'teacher'
    WHEN 'class.teacher@schoolerp.com' THEN 'class_teacher'
    WHEN 'hod.commerce@schoolerp.com' THEN 'hod_commerce'
    WHEN 'hod.science@schoolerp.com' THEN 'hod_science'
    ELSE 'teacher'
END
WHERE u.id BETWEEN 1 AND 5;

INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Models\\User', u.id
FROM users u, roles r
WHERE u.id BETWEEN 6 AND 10 AND r.name = 'subject_teacher';

-- 9. TEACHER PROFILES
INSERT INTO teacher_profiles (user_id, employee_id, phone, qualification, experience_years, specialization, designation, is_active, created_at, updated_at) VALUES
(1, 'EMP001', '+91 9876543201', 'M.Com, Ph.D', 20, 'Accounting', 'Principal', 1, NOW(), NOW()),
(2, 'EMP002', '+91 9876543202', 'M.Com, B.Ed', 10, 'Management', 'Senior Lecturer', 1, NOW(), NOW()),
(3, 'EMP003', '+91 9876543203', 'M.Com, B.Ed', 8, 'Accounting', 'Lecturer', 1, NOW(), NOW()),
(4, 'EMP004', '+91 9876543204', 'M.Com, Ph.D', 15, 'Commerce', 'Head of Department', 1, NOW(), NOW()),
(5, 'EMP005', '+91 9876543205', 'M.Sc, Ph.D', 15, 'Science', 'Head of Department', 1, NOW(), NOW()),
(6, 'EMP006', '+91 9876543206', 'M.Sc, B.Ed', 12, 'Mathematics', 'Lecturer', 1, NOW(), NOW()),
(7, 'EMP007', '+91 9876543207', 'M.A, B.Ed', 10, 'English', 'Lecturer', 1, NOW(), NOW()),
(8, 'EMP008', '+91 9876543208', 'M.Sc, B.Ed', 10, 'Physics', 'Lecturer', 1, NOW(), NOW()),
(9, 'EMP009', '+91 9876543209', 'M.Sc, B.Ed', 8, 'Chemistry', 'Lecturer', 1, NOW(), NOW()),
(10, 'EMP010', '+91 9876543210', 'M.Sc, B.Ed', 6, 'Biology', 'Lecturer', 1, NOW(), NOW());

-- 10. TEACHER ASSIGNMENTS
INSERT INTO teacher_assignments (teacher_id, division_id, subject_id, assignment_type, is_primary, created_at, updated_at) VALUES
(1, 1, 1, 'division', 1, NOW(), NOW()),
(2, 1, 2, 'division', 1, NOW(), NOW()),
(3, 1, 1, 'division', 1, NOW(), NOW()),
(4, 1, 1, 'department', 1, NOW(), NOW()),
(5, 4, 9, 'department', 1, NOW(), NOW()),
(6, 1, 4, 'subject', 1, NOW(), NOW()),
(7, 1, 7, 'subject', 1, NOW(), NOW()),
(8, 4, 9, 'subject', 1, NOW(), NOW()),
(9, 4, 10, 'subject', 1, NOW(), NOW()),
(10, 4, 12, 'subject', 1, NOW(), NOW());

-- 11. HOLIDAYS
INSERT INTO holidays (title, description, start_date, end_date, type, is_recurring, academic_year_id, program_incharge_id, location, is_active, created_at, updated_at) VALUES
('Republic Day', 'National Holiday', '2026-01-26', '2026-01-26', 'public_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Independence Day', 'National Holiday', '2026-08-15', '2026-08-15', 'public_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Gandhi Jayanti', 'National Holiday', '2026-10-02', '2026-10-02', 'public_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Diwali Break', 'Festival Holiday', '2026-11-10', '2026-11-12', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Christmas Break', 'Festival Holiday', '2026-12-24', '2026-12-26', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Annual Sports Day', 'Sports event', '2026-03-15', '2026-03-15', 'program', 0, 1, 2, 'Main Ground', 1, NOW(), NOW()),
('Annual Day', 'Cultural program', '2026-04-20', '2026-04-20', 'program', 0, 1, 3, 'Auditorium', 1, NOW(), NOW()),
('Science Exhibition', 'Science projects', '2026-05-10', '2026-05-12', 'event', 0, 1, 5, 'Science Block', 1, NOW(), NOW()),
('Summer Break', 'Summer vacation', '2026-05-15', '2026-06-30', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Teacher''s Day', 'Teachers celebration', '2026-09-05', '2026-09-05', 'event', 0, 1, 1, 'Main Hall', 1, NOW(), NOW()),
('Holi', 'Festival of Colors', '2026-03-07', '2026-03-07', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Eid', 'End of Ramadan', '2026-04-10', '2026-04-10', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Maharashtra Day', 'State Holiday', '2026-05-01', '2026-05-01', 'public_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Ganesh Chaturthi', 'Lord Ganesha festival', '2026-08-25', '2026-08-28', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW()),
('Navratri', 'Nine nights festival', '2026-10-15', '2026-10-18', 'school_holiday', 0, 1, NULL, NULL, 1, NOW(), NOW());

-- 12. TIMETABLES (50 records)
INSERT INTO timetables (division_id, subject_id, teacher_id, day_of_week, start_time, end_time, period_name, room_number, academic_year_id, is_active, created_at, updated_at) VALUES
(1, 1, 3, 'monday', '09:00:00', '10:00:00', 'Period 1', 'Room 101', 1, 1, NOW(), NOW()),
(1, 2, 2, 'monday', '10:00:00', '11:00:00', 'Period 2', 'Room 101', 1, 1, NOW(), NOW()),
(1, 3, 4, 'monday', '11:00:00', '12:00:00', 'Period 3', 'Room 101', 1, 1, NOW(), NOW()),
(1, 4, 6, 'monday', '12:00:00', '13:00:00', 'Period 4', 'Room 101', 1, 1, NOW(), NOW()),
(1, 5, 2, 'monday', '14:00:00', '15:00:00', 'Period 5', 'Room 101', 1, 1, NOW(), NOW()),
(1, 6, 3, 'monday', '15:00:00', '16:00:00', 'Period 6', 'Computer Lab', 1, 1, NOW(), NOW()),
(1, 2, 2, 'tuesday', '09:00:00', '10:00:00', 'Period 1', 'Room 101', 1, 1, NOW(), NOW()),
(1, 3, 4, 'tuesday', '10:00:00', '11:00:00', 'Period 2', 'Room 101', 1, 1, NOW(), NOW()),
(1, 1, 3, 'tuesday', '11:00:00', '12:00:00', 'Period 3', 'Room 101', 1, 1, NOW(), NOW()),
(1, 6, 3, 'tuesday', '12:00:00', '13:00:00', 'Period 4', 'Computer Lab', 1, 1, NOW(), NOW()),
(1, 7, 7, 'tuesday', '14:00:00', '15:00:00', 'Period 5', 'Room 101', 1, 1, NOW(), NOW()),
(1, 8, 4, 'tuesday', '15:00:00', '16:00:00', 'Period 6', 'Room 101', 1, 1, NOW(), NOW()),
(1, 3, 4, 'wednesday', '09:00:00', '10:00:00', 'Period 1', 'Room 101', 1, 1, NOW(), NOW()),
(1, 1, 3, 'wednesday', '10:00:00', '11:00:00', 'Period 2', 'Room 101', 1, 1, NOW(), NOW()),
(1, 4, 6, 'wednesday', '11:00:00', '12:00:00', 'Period 3', 'Room 101', 1, 1, NOW(), NOW()),
(1, 2, 2, 'wednesday', '12:00:00', '13:00:00', 'Period 4', 'Room 101', 1, 1, NOW(), NOW()),
(1, 5, 2, 'wednesday', '14:00:00', '15:00:00', 'Period 5', 'Room 101', 1, 1, NOW(), NOW()),
(1, 7, 7, 'wednesday', '15:00:00', '16:00:00', 'Period 6', 'Room 101', 1, 1, NOW(), NOW()),
(1, 4, 6, 'thursday', '09:00:00', '10:00:00', 'Period 1', 'Room 101', 1, 1, NOW(), NOW()),
(1, 5, 2, 'thursday', '10:00:00', '11:00:00', 'Period 2', 'Room 101', 1, 1, NOW(), NOW()),
(1, 6, 3, 'thursday', '11:00:00', '12:00:00', 'Period 3', 'Computer Lab', 1, 1, NOW(), NOW()),
(1, 7, 7, 'thursday', '12:00:00', '13:00:00', 'Period 4', 'Room 101', 1, 1, NOW(), NOW()),
(1, 1, 3, 'thursday', '14:00:00', '15:00:00', 'Period 5', 'Room 101', 1, 1, NOW(), NOW()),
(1, 8, 4, 'thursday', '15:00:00', '16:00:00', 'Period 6', 'Room 101', 1, 1, NOW(), NOW()),
(1, 5, 2, 'friday', '09:00:00', '10:00:00', 'Period 1', 'Room 101', 1, 1, NOW(), NOW()),
(1, 6, 3, 'friday', '10:00:00', '11:00:00', 'Period 2', 'Computer Lab', 1, 1, NOW(), NOW()),
(1, 7, 7, 'friday', '11:00:00', '12:00:00', 'Period 3', 'Room 101', 1, 1, NOW(), NOW()),
(1, 8, 4, 'friday', '12:00:00', '13:00:00', 'Period 4', 'Room 101', 1, 1, NOW(), NOW()),
(1, 2, 2, 'friday', '14:00:00', '15:00:00', 'Period 5', 'Room 101', 1, 1, NOW(), NOW()),
(1, 3, 4, 'friday', '15:00:00', '16:00:00', 'Period 6', 'Room 101', 1, 1, NOW(), NOW()),
(2, 1, 3, 'monday', '09:00:00', '10:00:00', 'Period 1', 'Room 201', 1, 1, NOW(), NOW()),
(2, 2, 2, 'monday', '10:00:00', '11:00:00', 'Period 2', 'Room 201', 1, 1, NOW(), NOW()),
(2, 3, 4, 'monday', '11:00:00', '12:00:00', 'Period 3', 'Room 201', 1, 1, NOW(), NOW()),
(2, 4, 6, 'monday', '12:00:00', '13:00:00', 'Period 4', 'Room 201', 1, 1, NOW(), NOW()),
(2, 2, 2, 'tuesday', '09:00:00', '10:00:00', 'Period 1', 'Room 201', 1, 1, NOW(), NOW()),
(2, 3, 4, 'tuesday', '10:00:00', '11:00:00', 'Period 2', 'Room 201', 1, 1, NOW(), NOW()),
(2, 1, 3, 'tuesday', '11:00:00', '12:00:00', 'Period 3', 'Room 201', 1, 1, NOW(), NOW()),
(2, 7, 7, 'tuesday', '12:00:00', '13:00:00', 'Period 4', 'Room 201', 1, 1, NOW(), NOW()),
(2, 4, 6, 'wednesday', '09:00:00', '10:00:00', 'Period 1', 'Room 201', 1, 1, NOW(), NOW()),
(2, 5, 2, 'wednesday', '10:00:00', '11:00:00', 'Period 2', 'Room 201', 1, 1, NOW(), NOW()),
(2, 6, 3, 'wednesday', '11:00:00', '12:00:00', 'Period 3', 'Computer Lab 2', 1, 1, NOW(), NOW()),
(2, 1, 3, 'wednesday', '12:00:00', '13:00:00', 'Period 4', 'Room 201', 1, 1, NOW(), NOW()),
(2, 4, 6, 'thursday', '09:00:00', '10:00:00', 'Period 1', 'Room 201', 1, 1, NOW(), NOW()),
(2, 5, 2, 'thursday', '10:00:00', '11:00:00', 'Period 2', 'Room 201', 1, 1, NOW(), NOW()),
(2, 7, 7, 'thursday', '11:00:00', '12:00:00', 'Period 3', 'Room 201', 1, 1, NOW(), NOW()),
(2, 8, 4, 'thursday', '12:00:00', '13:00:00', 'Period 4', 'Room 201', 1, 1, NOW(), NOW()),
(2, 6, 3, 'friday', '09:00:00', '10:00:00', 'Period 1', 'Computer Lab 2', 1, 1, NOW(), NOW()),
(2, 7, 7, 'friday', '10:00:00', '11:00:00', 'Period 2', 'Room 201', 1, 1, NOW(), NOW()),
(2, 8, 4, 'friday', '11:00:00', '12:00:00', 'Period 3', 'Room 201', 1, 1, NOW(), NOW()),
(2, 3, 4, 'friday', '12:00:00', '13:00:00', 'Period 4', 'Room 201', 1, 1, NOW(), NOW());

-- VERIFICATION
SELECT '✅ COMPLETE!' AS status;
SELECT 'Departments:', COUNT(*) FROM departments;
SELECT 'Programs:', COUNT(*) FROM programs;
SELECT 'Divisions:', COUNT(*) FROM divisions;
SELECT 'Subjects:', COUNT(*) FROM subjects;
SELECT 'Users:', COUNT(*) FROM users WHERE email LIKE '%@schoolerp.com';
SELECT 'Holidays:', COUNT(*) FROM holidays;
SELECT 'Timetables:', COUNT(*) FROM timetables;

SELECT '=== LOGIN CREDENTIALS ===' AS '';
SELECT 'principal@schoolerp.com / password' AS 'Login with:';
SELECT 'teacher@schoolerp.com / password' AS '';
SELECT 'class.teacher@schoolerp.com / password' AS '';
SELECT 'hod.commerce@schoolerp.com / password' AS '';
SELECT 'hod.science@schoolerp.com / password' AS '';
