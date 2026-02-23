<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Academic Structure
            DepartmentSeeder::class,
            ProgramSeeder::class,
            AcademicSessionSeeder::class,
            AcademicYearSeeder::class,
            DivisionSeeder::class,
            
            // Rooms and Time Slots (Phase 1 - Core Academic Integrity)
            RoomSeeder::class,
            TimeSlotSeeder::class,
            
            // Academic Rules (Phase 2 - Rule Engine)
            AcademicRuleSeeder::class,
            
            // Fee Structure
            FeeHeadSeeder::class,
            FeeStructureSeeder::class,
            ScholarshipSeeder::class,
            
            // Students
            StudentSeeder::class,
        ]);
    }
}