<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use App\Models\Academic\AcademicYear;
use Carbon\Carbon;

/**
 * Holiday Seeder
 * 
 * Populates the holidays table with sample data including:
 * - Public holidays (national festivals)
 * - School holidays (breaks, closures)
 * - Events (sports day, annual function)
 * - Programs (cultural fests, seminars)
 */
class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get current academic year
        $academicYear = AcademicYear::first();
        
        if (!$academicYear) {
            $this->command->error('No academic year found. Please run AcademicYearSeeder first.');
            return;
        }

        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;

        $holidays = [
            // ========== PUBLIC HOLIDAYS ==========
            [
                'title' => 'Republic Day',
                'description' => 'National Republic Day Celebration',
                'start_date' => "{$currentYear}-01-26",
                'end_date' => "{$currentYear}-01-26",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Independence Day',
                'description' => 'Independence Day of India',
                'start_date' => "{$currentYear}-08-15",
                'end_date' => "{$currentYear}-08-15",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Gandhi Jayanti',
                'description' => 'Birthday of Mahatma Gandhi',
                'start_date' => "{$currentYear}-10-02",
                'end_date' => "{$currentYear}-10-02",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Diwali',
                'description' => 'Festival of Lights - Diwali Celebration',
                'start_date' => "{$currentYear}-11-01",
                'end_date' => "{$currentYear}-11-03",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Holi',
                'description' => 'Festival of Colors',
                'start_date' => "{$nextYear}-03-14",
                'end_date' => "{$nextYear}-03-15",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Eid-ul-Fitr',
                'description' => 'Eid celebration marking end of Ramadan',
                'start_date' => "{$currentYear}-04-11",
                'end_date' => "{$currentYear}-04-11",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Christmas',
                'description' => 'Christmas Day Celebration',
                'start_date' => "{$currentYear}-12-25",
                'end_date' => "{$currentYear}-12-25",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Good Friday',
                'description' => 'Good Friday observance',
                'start_date' => "{$nextYear}-04-18",
                'end_date' => "{$nextYear}-04-18",
                'type' => 'public_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],

            // ========== SCHOOL HOLIDAYS ==========
            [
                'title' => 'Summer Break',
                'description' => 'Summer vacation for all classes',
                'start_date' => "{$nextYear}-05-15",
                'end_date' => "{$nextYear}-06-30",
                'type' => 'school_holiday',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Winter Break',
                'description' => 'Winter vacation for all classes',
                'start_date' => "{$currentYear}-12-24",
                'end_date' => "{$nextYear}-01-05",
                'type' => 'school_holiday',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Mid-Semester Break',
                'description' => 'Break between mid-semester and end semester',
                'start_date' => "{$currentYear}-10-15",
                'end_date' => "{$currentYear}-10-20",
                'type' => 'school_holiday',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Teacher\'s Day',
                'description' => 'No classes - Teacher appreciation day',
                'start_date' => "{$currentYear}-09-05",
                'end_date' => "{$currentYear}-09-05",
                'type' => 'school_holiday',
                'is_recurring' => true,
                'academic_year_id' => $academicYear->id,
                'location' => null,
                'is_active' => true,
            ],

            // ========== EVENTS ==========
            [
                'title' => 'Sports Day',
                'description' => 'Annual sports competition for all students',
                'start_date' => "{$currentYear}-11-15",
                'end_date' => "{$currentYear}-11-17",
                'type' => 'event',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => 'School Playground',
                'is_active' => true,
            ],
            [
                'title' => 'Annual Function',
                'description' => 'Cultural program and prize distribution',
                'start_date' => "{$nextYear}-02-28",
                'end_date' => "{$nextYear}-02-28",
                'type' => 'event',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => 'School Auditorium',
                'is_active' => true,
            ],
            [
                'title' => 'Science Exhibition',
                'description' => 'Science project exhibition and competition',
                'start_date' => "{$currentYear}-12-10",
                'end_date' => "{$currentYear}-12-12",
                'type' => 'event',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => 'Science Block',
                'is_active' => true,
            ],

            // ========== PROGRAMS ==========
            [
                'title' => 'Orientation Program',
                'description' => 'Orientation for new students',
                'start_date' => "{$currentYear}-07-01",
                'end_date' => "{$currentYear}-07-03",
                'type' => 'program',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => 'Main Hall',
                'is_active' => true,
            ],
            [
                'title' => 'Career Guidance Seminar',
                'description' => 'Career counseling and guidance program',
                'start_date' => "{$currentYear}-09-20",
                'end_date' => "{$currentYear}-09-20",
                'type' => 'program',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => 'Conference Room',
                'is_active' => true,
            ],
            [
                'title' => 'Alumni Meet',
                'description' => 'Annual alumni gathering and networking event',
                'start_date' => "{$nextYear}-01-15",
                'end_date' => "{$nextYear}-01-15",
                'type' => 'program',
                'is_recurring' => false,
                'academic_year_id' => $academicYear->id,
                'location' => 'School Campus',
                'is_active' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                [
                    'title' => $holiday['title'],
                    'start_date' => $holiday['start_date'],
                    'academic_year_id' => $holiday['academic_year_id'],
                ],
                $holiday
            );
        }

        $this->command->info('Holiday seeder completed successfully!');
        $this->command->info('Total holidays created: ' . count($holidays));
    }
}
