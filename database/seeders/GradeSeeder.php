<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['grade_name' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'grade_point' => 10, 'remarks' => 'Outstanding', 'is_active' => true],
            ['grade_name' => 'A', 'min_percentage' => 80, 'max_percentage' => 89.99, 'grade_point' => 9, 'remarks' => 'Excellent', 'is_active' => true],
            ['grade_name' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79.99, 'grade_point' => 8, 'remarks' => 'Very Good', 'is_active' => true],
            ['grade_name' => 'B', 'min_percentage' => 60, 'max_percentage' => 69.99, 'grade_point' => 7, 'remarks' => 'Good', 'is_active' => true],
            ['grade_name' => 'C', 'min_percentage' => 50, 'max_percentage' => 59.99, 'grade_point' => 6, 'remarks' => 'Average', 'is_active' => true],
            ['grade_name' => 'D', 'min_percentage' => 40, 'max_percentage' => 49.99, 'grade_point' => 5, 'remarks' => 'Pass', 'is_active' => true],
            ['grade_name' => 'F', 'min_percentage' => 0, 'max_percentage' => 39.99, 'grade_point' => 0, 'remarks' => 'Fail', 'is_active' => true],
        ];

        foreach ($grades as $grade) {
            Grade::create($grade);
        }
    }
}
