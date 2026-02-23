<?php

/**
 * =============================================================================
 * PROMOTION LOGS TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'promotion_logs' table for tracking all student
 * promotion events. Every promotion, demotion, or status change is logged
 * for audit and historical purposes.
 *
 * PURPOSE:
 * - Audit trail for all promotion decisions
 * - Track who promoted whom and when
 * - Enable rollback of incorrect promotions
 * - Support institutional reporting
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Audit logging for overrides (knowledge_base.md §11)
 * - Promotion workflow tracking (knowledge_base.md §5)
 * - Authorized overrides with reason (knowledge_base.md §11)
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
        Schema::create('promotion_logs', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // STUDENT REFERENCE
            // ========================================
            // Student being promoted
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // ========================================
            // FROM SESSION DETAILS
            // ========================================
            // Previous academic session
            $table->foreignId('from_academic_session_id')->constrained('academic_sessions');

            // Previous program
            $table->foreignId('from_program_id')->constrained('programs');

            // Previous academic year (FY, SY, TY)
            $table->string('from_academic_year', 20);

            // Previous division
            $table->foreignId('from_division_id')->constrained('divisions');

            // Previous result status
            $table->enum('from_result_status', [
                'prospect', 'active', 'exam_pending', 'pass',
                'atkt', 'fail', 'tc_issued', 'completed'
            ]);

            // ========================================
            // TO SESSION DETAILS
            // ========================================
            // New academic session (can be same if repeating)
            $table->foreignId('to_academic_session_id')->constrained('academic_sessions');

            // New program (can be same or different)
            $table->foreignId('to_program_id')->constrained('programs');

            // New academic year
            $table->string('to_academic_year', 20);

            // New division
            $table->foreignId('to_division_id')->constrained('divisions');

            // New result status
            $table->enum('to_result_status', [
                'prospect', 'active', 'exam_pending', 'pass',
                'atkt', 'fail', 'tc_issued', 'completed'
            ]);

            // ========================================
            // PROMOTION TYPE
            // ========================================
            // Type of promotion action
            // promoted: Normal promotion to next year
            // conditionally_promoted: ATKT promotion
            // repeated: Repeating same year
            // demoted: Moved back (rare, requires override)
            // transferred: Transferred to different program
            // tc_issued: Left institution
            $table->enum('promotion_type', [
                'promoted',
                'conditionally_promoted',
                'repeated',
                'demoted',
                'transferred',
                'tc_issued'
            ]);

            // ========================================
            // ELIGIBILITY SNAPSHOT
            // ========================================
            // Was student eligible for promotion?
            $table->boolean('was_eligible')->default(true);

            // Attendance percentage at time of promotion
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // Fee clearance status at time of promotion
            $table->boolean('fee_cleared')->default(true);

            // Backlog count at time of promotion
            $table->integer('backlog_count')->default(0);

            // ========================================
            // AUTHORIZATION & AUDIT
            // ========================================
            // User who performed the promotion
            $table->foreignId('promoted_by')->constrained('users');

            // User role at time of promotion
            $table->string('promoted_by_role', 50);

            // Was this an override (promotion despite ineligibility)?
            $table->boolean('is_override')->default(false);

            // Reason for override (if applicable)
            $table->text('override_reason')->nullable();

            // User who approved override (if different)
            $table->foreignId('override_approved_by')->nullable()->constrained('users');

            // ========================================
            // ACADEMIC RECORD REFERENCE
            // ========================================
            // Links to the new academic record created
            $table->foreignId('new_academic_record_id')
                  ->nullable()
                  ->constrained('student_academic_records');

            // ========================================
            // STATUS & METADATA
            // ========================================
            // Promotion status
            // pending: Scheduled but not executed
            // completed: Successfully executed
            // cancelled: Cancelled before execution
            // rolled_back: Rolled back after execution
            $table->enum('status', ['pending', 'completed', 'cancelled', 'rolled_back'])
                  ->default('completed');

            // Additional notes
            $table->text('notes')->nullable();

            // ========================================
            // TIMESTAMPS
            // ========================================
            $table->timestamps();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Index for student promotion history
            $table->index('student_id');

            // Index for session-wise promotions
            $table->index(['from_academic_session_id', 'to_academic_session_id']);

            // Index for override tracking
            $table->index(['is_override', 'promoted_by']);

            // Index for promotion type analysis
            $table->index('promotion_type');

            // Composite index for audit queries
            $table->index(['promoted_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_logs');
    }
};
