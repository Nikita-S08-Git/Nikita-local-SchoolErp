<?php

/**
 * =============================================================================
 * STUDENT ACADEMIC RECORDS TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'student_academic_records' table for session-wise
 * academic tracking. This allows students to have multiple academic records
 * across different sessions while maintaining historical integrity.
 *
 * PURPOSE:
 * - Track student's academic journey session by session
 * - Enable promotion without overwriting previous session data
 * - Support ATKT tracking across sessions
 * - Maintain immutable historical records
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Session-aware student tracking (knowledge_base.md §1.2)
 * - Immutable historical records (knowledge_base.md §4.4)
 * - State persistence per session (knowledge_base.md §4.4)
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
        Schema::create('student_academic_records', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // STUDENT REFERENCE
            // ========================================
            // Links to the core student identity
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // ========================================
            // ACADEMIC SESSION MAPPING
            // ========================================
            // Each record belongs to exactly one academic session
            $table->foreignId('academic_session_id')->constrained('academic_sessions');

            // ========================================
            // ACADEMIC STRUCTURE
            // ========================================
            // Program (B.Com, B.Sc, MBA, etc.)
            $table->foreignId('program_id')->constrained('programs');

            // Academic Year within program (FY, SY, TY, Semester 1, etc.)
            $table->string('academic_year', 20);

            // Division/Section (FY-A, SY-B, etc.)
            $table->foreignId('division_id')->constrained('divisions');

            // ========================================
            // ACADEMIC PERFORMANCE
            // ========================================
            // Overall result status for this session
            // prospect: Applied but not yet enrolled
            // active: Currently studying
            // exam_pending: Completed coursework, awaiting results
            // pass: Passed all subjects
            // atkt: Passed with ATKT (backlogs remain)
            // fail: Failed, must repeat year
            // tc_issued: Transfer certificate issued
            // completed: Final year completed successfully
            $table->enum('result_status', [
                'prospect',
                'active',
                'exam_pending',
                'pass',
                'atkt',
                'fail',
                'tc_issued',
                'completed'
            ])->default('active');

            // Promotion status to next session
            // not_eligible: Not eligible for promotion
            // eligible: Eligible for promotion
            // promoted: Successfully promoted
            // conditionally_promoted: Promoted with ATKT
            // repeated: Repeating same class
            // transferred: Transferred to another institution
            $table->enum('promotion_status', [
                'not_eligible',
                'eligible',
                'promoted',
                'conditionally_promoted',
                'repeated',
                'transferred'
            ])->default('not_eligible');

            // ========================================
            // ATKT TRACKING
            // ========================================
            // Number of backlog subjects at end of session
            $table->integer('backlog_count')->default(0);

            // Maximum ATKT attempts allowed for this record
            $table->integer('max_atkt_attempts')->default(3);

            // Current ATKT attempt number
            $table->integer('current_atkt_attempt')->default(0);

            // ========================================
            // ATTENDANCE SUMMARY
            // ========================================
            // Overall attendance percentage for this session
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // Attendance status (for eligibility checks)
            $table->enum('attendance_status', [
                'eligible',
                'not_eligible',
                'condonable'
            ])->default('eligible');

            // ========================================
            // FEE STATUS
            // ========================================
            // Fee clearance status for this session
            $table->boolean('fee_cleared')->default(true);

            // Outstanding amount (if any)
            $table->decimal('outstanding_amount', 10, 2)->default(0);

            // ========================================
            // SESSION LOCKING
            // ========================================
            // Whether this academic record is locked
            // Locked records cannot be modified
            $table->boolean('is_locked')->default(false);

            // Date when record was locked
            $table->timestamp('locked_at')->nullable();

            // User who locked the record
            $table->foreignId('locked_by')->nullable()->constrained('users');

            // ========================================
            // TIMESTAMPS & SOFT DELETE
            // ========================================
            $table->timestamps();
            $table->softDeletes();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Unique constraint: One active record per student per session
            $table->unique(['student_id', 'academic_session_id'], 'unique_student_session');

            // Index for finding students by session and status
            $table->index(['academic_session_id', 'result_status']);

            // Index for promotion queries
            $table->index(['academic_session_id', 'promotion_status']);

            // Index for ATKT tracking
            $table->index(['result_status', 'backlog_count']);

            // Composite index for eligibility checks
            $table->index(['attendance_status', 'fee_cleared', 'result_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_records');
    }
};
