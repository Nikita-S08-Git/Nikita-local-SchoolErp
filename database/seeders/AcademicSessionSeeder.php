<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\AcademicSession;

class AcademicSessionSeeder extends Seeder
{
    public function run(): void
    {
        AcademicSession::updateOrCreate(
            ['id' => 1],
            [
                'session_name' => '2024-25',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'is_current' => false,   // past session should not be current
                'is_active' => false,    // deactivated because 2025-26 is running
            ]
        );

        AcademicSession::updateOrCreate(
            ['id' => 2],
            [
                'session_name' => '2025-26',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'is_current' => true,    // active session now
                'is_active' => true,
            ]
        );

        // run helper to guarantee proper activation based on today's date
        AcademicSession::refreshActiveByDate();
    }
}