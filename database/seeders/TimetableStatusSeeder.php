<?php

namespace Database\Seeders;

use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\Academic\Subject;
use App\Models\User;
use App\Models\Academic\AcademicYear;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimetableStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates sample timetables with different statuses:
     * - Past date (Closed)
     * - Today's date (Active)
     * - Future date (Upcoming)
     */
    public function run(): void
    {
        $this->command->info('Creating sample timetables with different statuses...');
        
        // Get current academic year
        $academicYear = AcademicYear::getCurrentAcademicYear();
        if (!$academicYear) {
            $academicYear = AcademicYear::first();
        }
        
        if (!$academicYear) {
            $this->command->error('No academic year found. Please create an academic year first.');
            return;
        }
        
        // Get or create a division
        $division = Division::first();
        if (!$division) {
            $this->command->error('No division found. Please create a division first.');
            return;
        }
        
        // Get or create a subject
        $subject = Subject::first();
        if (!$subject) {
            $this->command->error('No subject found. Please create a subject first.');
            return;
        }
        
        // Get or create a teacher
        $teacher = User::role('teacher')->first();
        if (!$teacher) {
            $teacher = User::where('role', 'teacher')->first();
        }
        
        if (!$teacher) {
            $this->command->warn('No teacher found. Using first admin user.');
            $teacher = User::first();
        }
        
        $today = Carbon::today();
        
        // 1. Create PAST timetable (Closed) - Yesterday
        $yesterday = $today->copy()->subDay();
        $pastTimetable = Timetable::create([
            'division_id' => $division->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher?->id,
            'day_of_week' => strtolower($yesterday->format('l')),
            'date' => $yesterday->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'academic_year_id' => $academicYear->id,
            'status' => 'closed',
            'is_active' => true,
            'is_break_time' => false,
        ]);
        $this->command->info("Created PAST timetable (Closed): {$yesterday->format('Y-m-d')}");
        
        // 2. Create TODAY timetable (Active)
        $todayTimetable = Timetable::create([
            'division_id' => $division->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher?->id,
            'day_of_week' => strtolower($today->format('l')),
            'date' => $today->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
            'is_active' => true,
            'is_break_time' => false,
        ]);
        $this->command->info("Created TODAY timetable (Active): {$today->format('Y-m-d')}");
        
        // 3. Create FUTURE timetable (Upcoming) - Tomorrow
        $tomorrow = $today->copy()->addDay();
        $futureTimetable = Timetable::create([
            'division_id' => $division->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher?->id,
            'day_of_week' => strtolower($tomorrow->format('l')),
            'date' => $tomorrow->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'academic_year_id' => $academicYear->id,
            'status' => 'upcoming',
            'is_active' => true,
            'is_break_time' => false,
        ]);
        $this->command->info("Created FUTURE timetable (Upcoming): {$tomorrow->format('Y-m-d')}");
        
        // 4. Create another FUTURE timetable (Upcoming) - Next week
        $nextWeek = $today->copy()->addDays(7);
        $futureTimetable2 = Timetable::create([
            'division_id' => $division->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher?->id,
            'day_of_week' => strtolower($nextWeek->format('l')),
            'date' => $nextWeek->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'academic_year_id' => $academicYear->id,
            'status' => 'upcoming',
            'is_active' => true,
            'is_break_time' => false,
        ]);
        $this->command->info("Created FUTURE timetable (Upcoming): {$nextWeek->format('Y-m-d')}");
        
        // 5. Create WEEKLY timetable (no date - always Active)
        $weeklyTimetable = Timetable::create([
            'division_id' => $division->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher?->id,
            'day_of_week' => 'monday',
            'date' => null, // No specific date - weekly timetable
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
            'is_active' => true,
            'is_break_time' => false,
        ]);
        $this->command->info("Created WEEKLY timetable (Active): No specific date");
        
        $this->command->info('✅ Timetable status seeder completed!');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('  - 1 Closed (Past date)');
        $this->command->info('  - 2 Active (Today + Weekly)');
        $this->command->info('  - 2 Upcoming (Future dates)');
    }
}
