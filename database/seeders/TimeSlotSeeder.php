<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Seeding Time Slots ===" . PHP_EOL;

        $timeSlots = [
            ['slot_name' => 'Period 1', 'slot_code' => 'P1', 'start_time' => '09:00:00', 'end_time' => '10:00:00', 'slot_type' => 'instructional', 'sequence_order' => 1],
            ['slot_name' => 'Period 2', 'slot_code' => 'P2', 'start_time' => '10:00:00', 'end_time' => '11:00:00', 'slot_type' => 'instructional', 'sequence_order' => 2],
            ['slot_name' => 'Period 3', 'slot_code' => 'P3', 'start_time' => '11:00:00', 'end_time' => '12:00:00', 'slot_type' => 'instructional', 'sequence_order' => 3],
            ['slot_name' => 'Period 4', 'slot_code' => 'P4', 'start_time' => '12:00:00', 'end_time' => '13:00:00', 'slot_type' => 'instructional', 'sequence_order' => 4],
            ['slot_name' => 'Period 5', 'slot_code' => 'P5', 'start_time' => '14:00:00', 'end_time' => '15:00:00', 'slot_type' => 'instructional', 'sequence_order' => 5],
            ['slot_name' => 'Period 6', 'slot_code' => 'P6', 'start_time' => '15:00:00', 'end_time' => '16:00:00', 'slot_type' => 'instructional', 'sequence_order' => 6],
            ['slot_name' => 'Period 7', 'slot_code' => 'P7', 'start_time' => '16:00:00', 'end_time' => '17:00:00', 'slot_type' => 'instructional', 'sequence_order' => 7],
        ];

        foreach ($timeSlots as $slot) {
            TimeSlot::updateOrCreate(
                ['slot_code' => $slot['slot_code']],
                $slot
            );
            echo "  ✓ Created: {$slot['slot_name']} ({$slot['start_time']} - {$slot['end_time']})" . PHP_EOL;
        }

        echo PHP_EOL . "Time slots seeding complete!" . PHP_EOL;
    }
}
