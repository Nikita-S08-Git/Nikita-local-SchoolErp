<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\TimeSlot;

/**
 * Time Slot Seeder
 *
 * Seeds the time_slots table with standard class period and break definitions.
 * This provides initial time slot data for timetable scheduling.
 */
class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $timeSlots = [
            // Assembly
            [
                'slot_name' => 'Morning Assembly',
                'slot_code' => 'ASM',
                'start_time' => '08:00:00',
                'end_time' => '08:30:00',
                'slot_type' => TimeSlot::TYPE_ASSEMBLY,
                'sequence_order' => 1,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => false,
                'available_for_exams' => false,
                'description' => 'Morning assembly and announcements',
            ],

            // Period 1
            [
                'slot_name' => 'Period 1',
                'slot_code' => 'P1',
                'start_time' => '08:30:00',
                'end_time' => '09:25:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 2,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'First instructional period',
            ],

            // Period 2
            [
                'slot_name' => 'Period 2',
                'slot_code' => 'P2',
                'start_time' => '09:25:00',
                'end_time' => '10:20:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 3,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'Second instructional period',
            ],

            // Short Break
            [
                'slot_name' => 'Short Break',
                'slot_code' => 'SB',
                'start_time' => '10:20:00',
                'end_time' => '10:35:00',
                'slot_type' => TimeSlot::TYPE_BREAK,
                'sequence_order' => 4,
                'is_active' => true,
                'is_break' => true,
                'break_type' => TimeSlot::BREAK_TYPE_SHORT,
                'available_for_classes' => false,
                'available_for_exams' => false,
                'description' => 'Short refreshment break',
            ],

            // Period 3
            [
                'slot_name' => 'Period 3',
                'slot_code' => 'P3',
                'start_time' => '10:35:00',
                'end_time' => '11:30:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 5,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'Third instructional period',
            ],

            // Period 4
            [
                'slot_name' => 'Period 4',
                'slot_code' => 'P4',
                'start_time' => '11:30:00',
                'end_time' => '12:25:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 6,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'Fourth instructional period',
            ],

            // Lunch Break
            [
                'slot_name' => 'Lunch Break',
                'slot_code' => 'LB',
                'start_time' => '12:25:00',
                'end_time' => '13:10:00',
                'slot_type' => TimeSlot::TYPE_BREAK,
                'sequence_order' => 7,
                'is_active' => true,
                'is_break' => true,
                'break_type' => TimeSlot::BREAK_TYPE_LUNCH,
                'available_for_classes' => false,
                'available_for_exams' => false,
                'description' => 'Lunch break',
            ],

            // Period 5
            [
                'slot_name' => 'Period 5',
                'slot_code' => 'P5',
                'start_time' => '13:10:00',
                'end_time' => '14:05:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 8,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'Fifth instructional period',
            ],

            // Period 6
            [
                'slot_name' => 'Period 6',
                'slot_code' => 'P6',
                'start_time' => '14:05:00',
                'end_time' => '15:00:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 9,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'Sixth instructional period',
            ],

            // Period 7 (Optional/Extra)
            [
                'slot_name' => 'Period 7',
                'slot_code' => 'P7',
                'start_time' => '15:00:00',
                'end_time' => '15:50:00',
                'slot_type' => TimeSlot::TYPE_INSTRUCTIONAL,
                'sequence_order' => 10,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => true,
                'description' => 'Seventh instructional period (optional)',
            ],

            // Lab Slot (Extended)
            [
                'slot_name' => 'Lab Slot 1',
                'slot_code' => 'LS1',
                'start_time' => '09:25:00',
                'end_time' => '11:30:00',
                'slot_type' => TimeSlot::TYPE_LAB,
                'sequence_order' => 11,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => false,
                'available_for_exams' => false,
                'requires_room' => true,
                'description' => 'Extended laboratory slot (2 periods)',
            ],

            // Lab Slot 2 (Extended)
            [
                'slot_name' => 'Lab Slot 2',
                'slot_code' => 'LS2',
                'start_time' => '13:10:00',
                'end_time' => '15:00:00',
                'slot_type' => TimeSlot::TYPE_LAB,
                'sequence_order' => 12,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => false,
                'available_for_exams' => false,
                'requires_room' => true,
                'description' => 'Extended laboratory slot afternoon (2 periods)',
            ],

            // Exam Slot (Extended)
            [
                'slot_name' => 'Exam Slot Morning',
                'slot_code' => 'ESM',
                'start_time' => '10:00:00',
                'end_time' => '13:00:00',
                'slot_type' => TimeSlot::TYPE_EXAM,
                'sequence_order' => 13,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => false,
                'available_for_exams' => true,
                'description' => 'Morning examination slot (3 hours)',
            ],

            // Tutorial Slot
            [
                'slot_name' => 'Tutorial Period',
                'slot_code' => 'TP',
                'start_time' => '15:00:00',
                'end_time' => '16:00:00',
                'slot_type' => TimeSlot::TYPE_TUTORIAL,
                'sequence_order' => 14,
                'is_active' => true,
                'is_break' => false,
                'available_for_classes' => true,
                'available_for_exams' => false,
                'description' => 'Tutorial/remedial class period',
            ],
        ];

        foreach ($timeSlots as $slotData) {
            TimeSlot::create($slotData);
        }

        $this->command->info('Time slots seeded successfully: ' . count($timeSlots) . ' time slots created.');
    }
}
