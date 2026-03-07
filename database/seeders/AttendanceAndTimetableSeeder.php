<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttendanceAndTimetableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Starting Attendance & Timetable Data Seeding...');
        
        $this->call([
            TimetableSeeder::class,
            AttendanceSeeder::class,
        ]);
        
        $this->command->info('✅ Attendance & Timetable data seeded successfully!');
    }
}
