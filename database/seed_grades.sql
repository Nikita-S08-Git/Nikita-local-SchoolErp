-- Insert grade data into the grades table

INSERT INTO grades (grade_name, min_percentage, max_percentage, grade_point, remarks, is_active, created_at, updated_at) VALUES 
('A+', 90.00, 100.00, 10.00, 'Outstanding', 1, NOW(), NOW()),
('A', 80.00, 89.99, 9.00, 'Excellent', 1, NOW(), NOW()),
('B+', 70.00, 79.99, 8.00, 'Very Good', 1, NOW(), NOW()),
('B', 60.00, 69.99, 7.00, 'Good', 1, NOW(), NOW()),
('C', 50.00, 59.99, 6.00, 'Average', 1, NOW(), NOW()),
('D', 40.00, 49.99, 5.00, 'Pass', 1, NOW(), NOW()),
('F', 0.00, 39.99, 0.00, 'Fail', 1, NOW(), NOW());
