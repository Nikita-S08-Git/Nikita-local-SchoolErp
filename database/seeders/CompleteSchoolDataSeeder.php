<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompleteSchoolDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('');
        $this->command->info('🏫 ═══════════════════════════════════════════════════');
        $this->command->info('   SCHOOL ERP SYSTEM - COMPLETE DATA SEEDING');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('');

        $seeders = [
            ['class' => GradeSeeder::class, 'name' => 'Grades', 'icon' => '📊'],
            ['class' => AcademicSessionSeeder::class, 'name' => 'Academic Sessions', 'icon' => '📅'],
            ['class' => ProgramSeeder::class, 'name' => 'Programs', 'icon' => '🎓'],
            ['class' => DivisionSeeder::class, 'name' => 'Divisions', 'icon' => '🏛️'],
            ['class' => TeacherSeeder::class, 'name' => 'Teachers', 'icon' => '👨🏫'],
            ['class' => StudentSeeder::class, 'name' => 'Students', 'icon' => '👨🎓'],
            ['class' => FeeDataSeeder::class, 'name' => 'Fee Structures', 'icon' => '💰'],
            ['class' => ExaminationSeeder::class, 'name' => 'Examinations', 'icon' => '📝'],
            ['class' => DetailedTimetableSeeder::class, 'name' => 'Timetable', 'icon' => '🗓️'],
            ['class' => AttendanceSeeder::class, 'name' => 'Attendance', 'icon' => '✅'],
        ];

        foreach ($seeders as $index => $seeder) {
            $step = $index + 1;
            $total = count($seeders);
            
            $this->command->info("[$step/$total] {$seeder['icon']} Seeding {$seeder['name']}...");
            
            try {
                $this->call($seeder['class']);
                $this->command->info("      ✓ {$seeder['name']} completed");
            } catch (\Exception $e) {
                $this->command->warn("      ⚠ {$seeder['name']} skipped: " . $e->getMessage());
            }
            
            $this->command->info('');
        }

        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('✅ COMPLETE DATA SEEDING FINISHED!');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🌐 Access your School ERP at:');
        $this->command->info('   http://127.0.0.1:8000');
        $this->command->info('');
        $this->command->info('📚 Quick Links:');
        $this->command->info('   • Timetable: /academic/timetable');
        $this->command->info('   • Attendance: /academic/attendance');
        $this->command->info('   • Students: /dashboard/students');
        $this->command->info('   • Teachers: /dashboard/teachers');
        $this->command->info('   • Examinations: /examinations');
        $this->command->info('   • Reports: /reports/attendance');
        $this->command->info('');
    }
}
