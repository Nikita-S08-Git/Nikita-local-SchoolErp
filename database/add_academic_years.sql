-- ============================================
-- ADD MISSING ACADEMIC YEARS DATA
-- Run this in phpMyAdmin SQL tab
-- ============================================

USE schoolerp;

-- Insert Academic Years
INSERT INTO academic_years (id, name, start_date, end_date, is_active, created_at, updated_at) VALUES
(1, '2024-25', '2024-06-01', '2025-05-31', 1, NOW(), NOW()),
(2, '2025-26', '2025-06-01', '2026-05-31', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Insert Academic Sessions
INSERT INTO academic_sessions (id, session_name, start_date, end_date, is_active, created_at, updated_at) VALUES
(1, '2024-25', '2024-06-01', '2025-05-31', 1, NOW(), NOW()),
(2, '2025-26', '2025-06-01', '2026-05-31', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE session_name=VALUES(session_name);

-- Verify data
SELECT '✅ ACADEMIC YEARS ADDED!' AS status;
SELECT * FROM academic_years;
SELECT * FROM academic_sessions;
