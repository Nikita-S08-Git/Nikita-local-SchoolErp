-- Insert examination data into the examinations table

INSERT INTO examinations (name, code, type, start_date, end_date, academic_year, status, created_at, updated_at) VALUES
('First Unit Test', 'UT1-2025', 'internal', '2025-11-01', '2025-11-06', '2025-2026', 'completed', NOW(), NOW()),
('Second Unit Test', 'UT2-2025', 'internal', '2026-01-10', '2026-01-15', '2025-2026', 'completed', NOW(), NOW()),
('Third Unit Test', 'UT3-2026', 'internal', '2026-02-15', '2026-02-20', '2025-2026', 'completed', NOW(), NOW()),
('Mid-Term Examination', 'MID-2025', 'internal', '2025-12-01', '2025-12-12', '2025-2026', 'completed', NOW(), NOW()),
('Mid-Term Examination', 'MID-2026', 'internal', '2026-03-01', '2026-03-12', '2025-2026', 'ongoing', NOW(), NOW()),
('Final Examination', 'FINAL-2025', 'external', '2026-04-01', '2026-04-15', '2025-2026', 'scheduled', NOW(), NOW()),
('Final Examination', 'FINAL-2026', 'external', '2026-05-01', '2026-05-20', '2025-2026', 'scheduled', NOW(), NOW()),
('Practical Examination', 'PRAC-2025', 'practical', '2026-03-20', '2026-03-25', '2025-2026', 'scheduled', NOW(), NOW()),
('Science Practical', 'SCI-PRAC-2026', 'practical', '2026-03-10', '2026-03-15', '2025-2026', 'completed', NOW(), NOW()),
('Computer Practical', 'COMP-PRAC-2026', 'practical', '2026-03-16', '2026-03-18', '2025-2026', 'completed', NOW(), NOW()),
('Annual Examination', 'ANN-2026', 'external', '2026-04-20', '2026-04-30', '2025-2026', 'scheduled', NOW(), NOW()),
('Quarterly Exam', 'QTR-2025', 'internal', '2025-10-01', '2025-10-10', '2025-2026', 'completed', NOW(), NOW()),
('Half-Yearly Exam', 'HY-2025', 'external', '2025-11-15', '2025-11-30', '2025-2026', 'completed', NOW(), NOW()),
('Supplementary Exam', 'SUPP-2026', 'external', '2026-06-01', '2026-06-10', '2025-2026', 'scheduled', NOW(), NOW());
