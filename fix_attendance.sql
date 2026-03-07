-- Add academic_session_id column to attendance table
-- Run this in phpMyAdmin or MySQL command line

-- Check if column exists first
SELECT COUNT(*) as column_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'attendance' 
AND COLUMN_NAME = 'academic_session_id';

-- If column doesn't exist, run this ALTER statement
ALTER TABLE attendance 
ADD COLUMN academic_session_id BIGINT UNSIGNED NULL AFTER division_id,
ADD CONSTRAINT fk_attendance_academic_session 
FOREIGN KEY (academic_session_id) 
REFERENCES academic_sessions(id) 
ON DELETE CASCADE;

-- Add index for better query performance
ALTER TABLE attendance 
ADD INDEX idx_attendance_academic_session (academic_session_id);
