<?php

/**
 * =============================================================================
 * ROOMS TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'rooms' table for classroom and laboratory
 * management. Rooms are essential for conflict-free timetable scheduling.
 *
 * PURPOSE:
 * - Define all classrooms and labs in the institution
 * - Track room capacity for conflict prevention
 * - Support room allocation in timetables
 * - Enable room utilization reporting
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Room conflict prevention (knowledge_base.md §4.2)
 * - Capacity validation (knowledge_base.md §4.2)
 * - Room entity for scheduling (knowledge_base.md §4.1)
 * =============================================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // ROOM IDENTIFICATION
            // ========================================
            // Room number/name (e.g., "Room 101", "Lab A")
            $table->string('room_number', 50)->unique();

            // Room name (e.g., "Computer Lab 1", "Seminar Hall")
            $table->string('name', 100)->nullable();

            // ========================================
            // ROOM TYPE
            // ========================================
            // Type of room
            // classroom: Regular classroom
            // lab: Computer/Science laboratory
            // seminar_hall: Large hall for lectures
            // auditorium: Main auditorium
            // library_room: Library reading room
            // other: Other types
            $table->enum('room_type', [
                'classroom',
                'lab',
                'seminar_hall',
                'auditorium',
                'library_room',
                'other'
            ])->default('classroom');

            // ========================================
            // CAPACITY & DIMENSIONS
            // ========================================
            // Maximum seating capacity
            $table->integer('capacity')->default(30);

            // Floor number (for multi-building campuses)
            $table->integer('floor_number')->default(1);

            // Building name/block
            $table->string('building_block', 50)->nullable();

            // ========================================
            // FACILITIES & EQUIPMENT
            // ========================================
            // Has projector
            $table->boolean('has_projector')->default(false);

            // Has smart board
            $table->boolean('has_smart_board')->default(false);

            // Has computer systems
            $table->boolean('has_computers')->default(false);

            // Number of computer systems (for labs)
            $table->integer('computer_count')->default(0);

            // Has air conditioning
            $table->boolean('has_ac')->default(false);

            // Has wheelchair access
            $table->boolean('is_wheelchair_accessible')->default(false);

            // ========================================
            // AVAILABILITY & STATUS
            // ========================================
            // Room status
            // available: Available for scheduling
            // under_maintenance: Not available
            // blocked: Temporarily blocked
            // deprecated: No longer in use
            $table->enum('status', ['available', 'under_maintenance', 'blocked', 'deprecated'])
                  ->default('available');

            // Maintenance notes (if under maintenance)
            $table->text('maintenance_notes')->nullable();

            // ========================================
            // SCHEDULING CONSTRAINTS
            // ========================================
            // Days when room is not available (e.g., for maintenance)
            // Stored as JSON array: ["Monday", "Saturday"]
            $table->json('unavailable_days')->nullable();

            // Time slots when room is not available
            // Stored as JSON array of time ranges
            $table->json('unavailable_time_slots')->nullable();

            // Minimum booking duration in minutes
            $table->integer('min_booking_duration')->default(30);

            // Maximum booking duration in minutes
            $table->integer('max_booking_duration')->default(300);

            // ========================================
            // DEPARTMENT ASSIGNMENT (Optional)
            // ========================================
            // Department that primarily uses this room
            $table->foreignId('department_id')->nullable()->constrained('departments');

            // Is this room department-specific?
            $table->boolean('is_department_specific')->default(false);

            // ========================================
            // USAGE TRACKING
            // ========================================
            // Total hours used this academic session (for reporting)
            $table->integer('total_hours_used')->default(0);

            // Utilization percentage (calculated field, stored for reporting)
            $table->decimal('utilization_percentage', 5, 2)->default(0);

            // ========================================
            // AUDIT & METADATA
            // ========================================
            // Description of the room
            $table->text('description')->nullable();

            // Any special notes
            $table->text('notes')->nullable();

            // User who added this room
            $table->foreignId('created_by')->nullable()->constrained('users');

            // User who last updated this room
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // ========================================
            // TIMESTAMPS & SOFT DELETE
            // ========================================
            $table->timestamps();
            $table->softDeletes();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Index for room type filtering
            $table->index('room_type');

            // Index for status filtering
            $table->index('status');

            // Index for capacity-based searches
            $table->index('capacity');

            // Index for department filtering
            $table->index('department_id');

            // Composite index for availability checks
            $table->index(['status', 'room_type', 'capacity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
