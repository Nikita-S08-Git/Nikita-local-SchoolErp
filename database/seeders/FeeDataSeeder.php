<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee\FeeHead;
use App\Models\Fee\FeeStructure;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicSession;

class FeeDataSeeder extends Seeder
{
    public function run()
    {
        $session = AcademicSession::where('is_active', true)->first();
        $programs = Program::where('is_active', true)->get();

        if (!$session || $programs->isEmpty()) {
            $this->command->warn('No active session or programs found.');
            return;
        }

        // Create Fee Heads
        $feeHeads = [
            ['fee_head_name' => 'Tuition Fee', 'description' => 'Monthly tuition fee', 'is_active' => true],
            ['fee_head_name' => 'Admission Fee', 'description' => 'One-time admission fee', 'is_active' => true],
            ['fee_head_name' => 'Exam Fee', 'description' => 'Examination fee', 'is_active' => true],
            ['fee_head_name' => 'Library Fee', 'description' => 'Library access fee', 'is_active' => true],
            ['fee_head_name' => 'Sports Fee', 'description' => 'Sports and activities fee', 'is_active' => true],
        ];

        foreach ($feeHeads as $head) {
            FeeHead::firstOrCreate(['fee_head_name' => $head['fee_head_name']], $head);
        }

        // Create Fee Structures
        $tuitionHead = FeeHead::where('fee_head_name', 'Tuition Fee')->first();
        
        foreach ($programs as $program) {
            FeeStructure::firstOrCreate([
                'fee_head_id' => $tuitionHead->id,
                'program_id' => $program->id,
                'academic_session_id' => $session->id,
            ], [
                'amount' => rand(5000, 15000),
                'frequency' => 'monthly',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Created fee heads and structures');
    }
}
