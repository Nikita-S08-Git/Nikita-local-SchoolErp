<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Room;

/**
 * Room Seeder
 *
 * Seeds the rooms table with sample classroom and laboratory data.
 * This provides initial room data for timetable scheduling.
 */
class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $rooms = [
            // Classrooms - Building A
            [
                'room_number' => 'A-101',
                'name' => 'Classroom A1',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 60,
                'floor_number' => 1,
                'building_block' => 'Block A',
                'has_projector' => true,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Ground floor classroom with projector',
            ],
            [
                'room_number' => 'A-102',
                'name' => 'Classroom A2',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 60,
                'floor_number' => 1,
                'building_block' => 'Block A',
                'has_projector' => true,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Ground floor classroom with projector',
            ],
            [
                'room_number' => 'A-201',
                'name' => 'Classroom A3',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 60,
                'floor_number' => 2,
                'building_block' => 'Block A',
                'has_projector' => false,
                'has_smart_board' => true,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => false,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Second floor classroom with smart board',
            ],
            [
                'room_number' => 'A-202',
                'name' => 'Classroom A4',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 60,
                'floor_number' => 2,
                'building_block' => 'Block A',
                'has_projector' => false,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => false,
                'is_wheelchair_accessible' => false,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Standard classroom',
            ],
            [
                'room_number' => 'A-301',
                'name' => 'Seminar Hall A',
                'room_type' => Room::TYPE_SEMINAR_HALL,
                'capacity' => 120,
                'floor_number' => 3,
                'building_block' => 'Block A',
                'has_projector' => true,
                'has_smart_board' => true,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Large seminar hall for events',
            ],

            // Classrooms - Building B
            [
                'room_number' => 'B-101',
                'name' => 'Classroom B1',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 50,
                'floor_number' => 1,
                'building_block' => 'Block B',
                'has_projector' => false,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => false,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Standard classroom',
            ],
            [
                'room_number' => 'B-102',
                'name' => 'Classroom B2',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 50,
                'floor_number' => 1,
                'building_block' => 'Block B',
                'has_projector' => true,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => false,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Classroom with projector',
            ],
            [
                'room_number' => 'B-201',
                'name' => 'Classroom B3',
                'room_type' => Room::TYPE_CLASSROOM,
                'capacity' => 50,
                'floor_number' => 2,
                'building_block' => 'Block B',
                'has_projector' => false,
                'has_smart_board' => true,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => false,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Classroom with smart board',
            ],

            // Computer Labs
            [
                'room_number' => 'LAB-C1',
                'name' => 'Computer Lab 1',
                'room_type' => Room::TYPE_LAB,
                'capacity' => 40,
                'floor_number' => 1,
                'building_block' => 'Block C',
                'has_projector' => true,
                'has_smart_board' => false,
                'has_computers' => true,
                'computer_count' => 40,
                'has_ac' => true,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Main computer laboratory with 40 systems',
            ],
            [
                'room_number' => 'LAB-C2',
                'name' => 'Computer Lab 2',
                'room_type' => Room::TYPE_LAB,
                'capacity' => 30,
                'floor_number' => 1,
                'building_block' => 'Block C',
                'has_projector' => false,
                'has_smart_board' => false,
                'has_computers' => true,
                'computer_count' => 30,
                'has_ac' => true,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Secondary computer laboratory',
            ],

            // Science Labs
            [
                'room_number' => 'LAB-S1',
                'name' => 'Physics Lab',
                'room_type' => Room::TYPE_LAB,
                'capacity' => 30,
                'floor_number' => 2,
                'building_block' => 'Block C',
                'has_projector' => false,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => false,
                'is_wheelchair_accessible' => false,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Physics laboratory with experiments setup',
            ],
            [
                'room_number' => 'LAB-S2',
                'name' => 'Chemistry Lab',
                'room_type' => Room::TYPE_LAB,
                'capacity' => 30,
                'floor_number' => 2,
                'building_block' => 'Block C',
                'has_projector' => false,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => false,
                'is_wheelchair_accessible' => false,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Chemistry laboratory with fume hoods',
            ],
            [
                'room_number' => 'LAB-S3',
                'name' => 'Biology Lab',
                'room_type' => Room::TYPE_LAB,
                'capacity' => 30,
                'floor_number' => 2,
                'building_block' => 'Block C',
                'has_projector' => true,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => false,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Biology laboratory with microscope stations',
            ],

            // Auditorium
            [
                'room_number' => 'AUD-1',
                'name' => 'Main Auditorium',
                'room_type' => Room::TYPE_AUDITORIUM,
                'capacity' => 500,
                'floor_number' => 1,
                'building_block' => 'Block D',
                'has_projector' => true,
                'has_smart_board' => false,
                'has_computers' => false,
                'has_ac' => true,
                'is_wheelchair_accessible' => true,
                'status' => Room::STATUS_AVAILABLE,
                'description' => 'Main auditorium for college events and functions',
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        $this->command->info('Rooms seeded successfully: ' . count($rooms) . ' rooms created.');
    }
}
