<?php

/**
 * =============================================================================
 * TIME SLOTS TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'time_slots' table for standardized time slot
 * definition. Time slots are essential for conflict-free timetable scheduling.
 *
 * PURPOSE:
 * - Define standard class periods and break times
 * - Prevent overlapping time slots
 * - Support timetable generation
 * - Enable time slot conflict detection
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Time slot entity for scheduling (knowledge_base.md §4.1)
 * - No overlapping time slots (knowledge_base.md §2.3)
 * - Conflict detection basis (knowledge_base.md §4.2)
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
        Schema::create('time_slots', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // TIME SLOT IDENTIFICATION
            // ========================================
            // Slot name/label (e.g., "Period 1", "Lunch Break")
            $table->string('slot_name', 50);

            // Slot code for reference (e.g., "P1", "LB", "P2")
            $table->string('slot_code', 10)->unique();

            // ========================================
            // TIMING DETAILS
            // ========================================
            // Start time (24-hour format, e.g., "09:00:00")
            $table->time('start_time');

            // End time (24-hour format, e.g., "10:00:00")
            $table->time('end_time');

            // ========================================
            // SLOT TYPE
            // ========================================
            // Type of time slot
            // instructional: Regular class period
            // break: Lunch/recess break
            // assembly: Morning assembly
            // exam: Examination period
            // lab: Laboratory session (extended)
            // tutorial: Tutorial/remedial class
            // other: Other types
            $table->enum('slot_type', [
                'instructional',
                'break',
                'assembly',
                'exam',
                'lab',
                'tutorial',
                'other'
            ])->default('instructional');

            // ========================================
            // DURATION
            // ========================================
            // Duration in minutes (calculated from start/end time)
            $table->integer('duration_minutes')->storedAs(
                // Laravel doesn't support storedAs in all databases, 
                // we'll calculate this in the model
                // This is a placeholder for documentation
                0
            );

            // ========================================
            // SEQUENCE & ORDERING
            // ========================================
            // Sequence order (1 = first slot, 2 = second, etc.)
            $table->integer('sequence_order')->default(1);

            // Is this slot active for scheduling?
            $table->boolean('is_active')->default(true);

            // ========================================
            // BREAK INDICATOR
            // ========================================
            // Is this a break slot (no class scheduled)?
            $table->boolean('is_break')->default(false);

            // If break, what type?
            // null: Not a break
            // short_break: 5-15 minute break
            // lunch: Lunch break
            // long_break: Extended break
            $table->enum('break_type', ['short_break', 'lunch', 'long_break'])
                  ->nullable();

            // ========================================
            // ACADEMIC SESSION MAPPING
            // ========================================
            // Academic session this time slot applies to
            // (allows different timings for different sessions)
            $table->foreignId('academic_session_id')
                  ->nullable()
                  ->constrained('academic_sessions');

            // Is this the default time slot for all sessions?
            $table->boolean('is_default')->default(false);

            // ========================================
            // DAYS APPLICABLE
            // ========================================
            // Days when this slot is active
            // Stored as JSON array: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]
            // Null means all days
            $table->json('applicable_days')->nullable();

            // ========================================
            // ROOM ASSIGNMENT (Optional)
            // ========================================
            // If this slot is for a specific room (e.g., lab slot)
            $table->foreignId('assigned_room_id')->nullable()->constrained('rooms');

            // Is room assignment mandatory for this slot?
            $table->boolean('requires_room')->default(true);

            // ========================================
            // FACULTY ASSIGNMENT (Optional)
            // ========================================
            // If this slot is for a specific teacher (e.g., duty period)
            $table->foreignId('assigned_teacher_id')->nullable()->constrained('users');

            // ========================================
            // CONSTRAINTS & VALIDATION
            // ========================================
            // Minimum gap required before this slot (in minutes)
            $table->integer('min_gap_before')->default(0);

            // Minimum gap required after this slot (in minutes)
            $table->integer('min_gap_after')->default(0);

            // Can this slot be used for regular classes?
            $table->boolean('available_for_classes')->default(true);

            // Can this slot be used for exams?
            $table->boolean('available_for_exams')->default(true);

            // ========================================
            // USAGE TRACKING
            // ========================================
            // Maximum divisions that can use this slot simultaneously
            // (for multi-division scheduling)
            $table->integer('max_parallel_divisions')->default(1);

            // Current utilization count (for real-time tracking)
            $table->integer('current_utilization')->default(0);

            // ========================================
            // DESCRIPTION & NOTES
            // ========================================
            // Description of the time slot
            $table->text('description')->nullable();

            // Any special notes or constraints
            $table->text('notes')->nullable();

            // ========================================
            // AUDIT FIELDS
            // ========================================
            // User who created this time slot
            $table->foreignId('created_by')->nullable()->constrained('users');

            // User who last updated this time slot
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // ========================================
            // TIMESTAMPS & SOFT DELETE
            // ========================================
            $table->timestamps();
            $table->softDeletes();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Index for slot type filtering
            $table->index('slot_type');

            // Index for active slots
            $table->index('is_active');

            // Index for sequence ordering
            $table->index('sequence_order');

            // Index for academic session
            $table->index('academic_session_id');

            // Index for break slots
            $table->index('is_break');

            // Unique constraint: No overlapping slots for same session
            // (start_time, end_time, academic_session_id should be unique combination)
            // This is enforced at application level with validation

            // Composite index for availability checks
            $table->index(['is_active', 'available_for_classes', 'slot_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
