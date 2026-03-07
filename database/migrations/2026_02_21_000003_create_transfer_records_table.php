<?php

/**
 * =============================================================================
 * TRANSFER RECORDS TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'transfer_records' table for tracking student
 * transfers (leaving certificate / transfer certificate). This provides
 * a complete audit trail when students leave the institution.
 *
 * PURPOSE:
 * - Track student transfers out of institution
 * - Store transfer certificate details
 * - Maintain reason for leaving
 * - Enable transfer verification
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Student exit states (knowledge_base.md §1.3)
 * - Transfer/Leaving certificate (knowledge_base.md §3.3)
 * - Audit trail for lifecycle changes (knowledge_base.md §11)
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
        Schema::create('transfer_records', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // STUDENT REFERENCE
            // ========================================
            // Student being transferred
            $table->foreignId('student_id')->unique()->constrained('students')->onDelete('cascade');

            // ========================================
            // CURRENT ACADEMIC CONTEXT
            // ========================================
            // Academic session at time of transfer
            $table->foreignId('academic_session_id')->constrained('academic_sessions');

            // Program at time of transfer
            $table->foreignId('program_id')->constrained('programs');

            // Academic year at time of transfer
            $table->string('academic_year', 20);

            // Division at time of transfer
            $table->foreignId('division_id')->constrained('divisions');

            // ========================================
            // TRANSFER DETAILS
            // ========================================
            // Transfer certificate number (unique)
            $table->string('tc_number', 50)->unique();

            // Transfer type
            // voluntary: Student chose to leave
            // expulsion: Disciplinary action
            // academic_dismissal: Failed too many times
            // financial: Non-payment of fees
            // medical: Medical reasons
            // family_relocation: Family moved
            // course_completed: Finished course
            // other: Other reasons
            $table->enum('transfer_type', [
                'voluntary',
                'expulsion',
                'academic_dismissal',
                'financial',
                'medical',
                'family_relocation',
                'course_completed',
                'other'
            ]);

            // Reason for transfer (detailed)
            $table->text('reason')->nullable();

            // ========================================
            // TRANSFER CERTIFICATE DETAILS
            // ========================================
            // Date of transfer certificate issuance
            $table->date('tc_issue_date');

            // Last date of attendance
            $table->date('last_attendance_date');

            // Conduct/behavior remark
            $table->enum('conduct', ['excellent', 'good', 'fair', 'poor'])->default('good');

            // Whether student is eligible for re-admission
            $table->boolean('eligible_for_readmission')->default(true);

            // Readmission remarks (if not eligible)
            $table->text('readmission_remarks')->nullable();

            // ========================================
            // ACADEMIC STANDING AT TRANSFER
            // ========================================
            // Result status at time of transfer
            $table->enum('result_status', [
                'prospect', 'active', 'exam_pending', 'pass',
                'atkt', 'fail', 'tc_issued', 'completed'
            ]);

            // Attendance percentage at time of transfer
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // Fee clearance status
            $table->boolean('fee_cleared')->default(true);

            // Outstanding fee amount (if any)
            $table->decimal('outstanding_fees', 10, 2)->default(0);

            // Backlog count at time of transfer
            $table->integer('backlog_count')->default(0);

            // ========================================
            // DESTINATION INFORMATION (Optional)
            // ========================================
            // Name of institution student is transferring to
            $table->string('destination_institution', 255)->nullable();

            // City of destination institution
            $table->string('destination_city', 100)->nullable();

            // State of destination institution
            $table->string('destination_state', 100)->nullable();

            // Course student is joining at destination
            $table->string('destination_course', 255)->nullable();

            // ========================================
            // AUTHORIZATION & APPROVAL
            // ========================================
            // Principal/authority who approved transfer
            $table->foreignId('approved_by')->constrained('users');

            // User who processed the transfer
            $table->foreignId('processed_by')->constrained('users');

            // Transfer status
            // pending: Request submitted, awaiting approval
            // approved: Approved, TC ready
            // issued: TC handed to student
            // cancelled: Transfer cancelled
            $table->enum('status', ['pending', 'approved', 'issued', 'cancelled'])
                  ->default('issued');

            // ========================================
            // DOCUMENTATION
            // ========================================
            // Path to transfer certificate PDF
            $table->string('tc_document_path', 500)->nullable();

            // Any additional documents
            $table->json('additional_documents')->nullable();

            // ========================================
            // AUDIT & NOTES
            // ========================================
            // Was this an override (e.g., transfer despite fee dues)?
            $table->boolean('is_override')->default(false);

            // Override reason
            $table->text('override_reason')->nullable();

            // Additional notes
            $table->text('notes')->nullable();

            // ========================================
            // TIMESTAMPS & SOFT DELETE
            // ========================================
            $table->timestamps();
            $table->softDeletes();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Index for TC number lookup
            $table->index('tc_number');

            // Index for transfer type analysis
            $table->index('transfer_type');

            // Index for session-wise transfers
            $table->index('academic_session_id');

            // Index for status tracking
            $table->index('status');

            // Composite index for reporting
            $table->index(['transfer_type', 'academic_session_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_records');
    }
};
