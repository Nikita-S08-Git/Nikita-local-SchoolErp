-- Fix: Add academic_session_id column to attendance table
-- Run this SQL in phpMyAdmin or MySQL command line

-- Check if column exists first
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'attendance' 
    AND COLUMN_NAME = 'academic_session_id'
);

-- Add the column if it doesn't exist
ALTER TABLE attendance 
ADD COLUMN academic_session_id BIGINT UNSIGNED NULL AFTER division_id;

-- Add foreign key constraint (if academic_sessions table exists)
-- ALTER TABLE attendance 
-- ADD CONSTRAINT attendance_academic_session_id_foreign 
-- FOREIGN KEY (academic_session_id) 
-- REFERENCES academic_sessions(id) 
-- ON DELETE CASCADE;

-- Add index for better performance
ALTER TABLE attendance 
ADD INDEX idx_academic_session (academic_session_id);

SELECT 'Column academic_session_id added successfully!' AS Result;
